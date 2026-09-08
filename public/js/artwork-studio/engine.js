/* ============================================================
   Artwork Studio engine — pure client-side image processing.
   No API, no external service, no per-use cost.

   Pipeline:
     1. load + normalise        4. trace vector contours (marching squares)
     2. remove background       5. match to thread chart
     3. reduce to thread colours (k-means)
   ============================================================ */
window.ArtEngine = (function(){

  const WORK_MAX = 760;           // processing resolution (vector output is resolution-independent)

  /* ---------------- helpers ---------------- */
  function hexToRgb(h){ h=h.replace('#',''); return [parseInt(h.slice(0,2),16),parseInt(h.slice(2,4),16),parseInt(h.slice(4,6),16)]; }
  function rgbToHex(r,g,b){ return '#'+[r,g,b].map(v=>Math.max(0,Math.min(255,Math.round(v))).toString(16).padStart(2,'0')).join(''); }
  function lum(r,g,b){ return 0.299*r+0.587*g+0.114*b; }

  /* perceptual-ish RGB distance (redmean). distSq skips the sqrt for
     nearest-neighbour comparisons, which is all the hot loops need. */
  function distSq(r1,g1,b1,r2,g2,b2){
    const rm=(r1+r2)/2, dr=r1-r2, dg=g1-g2, db=b1-b2;
    return (2+rm/256)*dr*dr + 4*dg*dg + (2+(255-rm)/256)*db*db;
  }
  function dist(r1,g1,b1,r2,g2,b2){ return Math.sqrt(distSq(r1,g1,b1,r2,g2,b2)); }

  /* ---------------- 1. load ---------------- */
  function loadImage(src){
    return new Promise((res,rej)=>{
      const im=new Image();
      im.onload=()=>res(im); im.onerror=()=>rej(new Error('Could not read that image.'));
      im.src=src;
    });
  }

  function toWorkCanvas(img){
    const scale=Math.min(1, WORK_MAX/Math.max(img.width,img.height));
    const w=Math.max(1,Math.round(img.width*scale)), h=Math.max(1,Math.round(img.height*scale));
    const c=document.createElement('canvas'); c.width=w; c.height=h;
    const ctx=c.getContext('2d',{willReadFrequently:true});
    ctx.imageSmoothingQuality='high';
    ctx.drawImage(img,0,0,w,h);
    return c;
  }

  /* ---------------- 2. background removal ----------------
     Flood-fill inward from the border. Anything connected to the
     edge and within tolerance of the border colour becomes
     transparent. Handles photographed / scanned logos on paper.  */
  function removeBackground(imgData, tolerance){
    const {width:w,height:h,data:d}=imgData;
    const idx=(x,y)=>(y*w+x)*4;

    // sample the border to establish the background colour
    let sr=0,sg=0,sb=0,n=0;
    const sample=(x,y)=>{const i=idx(x,y); if(d[i+3]>10){sr+=d[i];sg+=d[i+1];sb+=d[i+2];n++;}};
    for(let x=0;x<w;x++){ sample(x,0); sample(x,h-1); }
    for(let y=0;y<h;y++){ sample(0,y); sample(w-1,y); }
    if(!n) return {removed:false};
    const br=sr/n, bg=sg/n, bb=sb/n;

    // if the border is busy (high variance) it's probably not a flat background
    let varsum=0;
    for(let x=0;x<w;x++){ const i=idx(x,0), j=idx(x,h-1);
      varsum+=dist(d[i],d[i+1],d[i+2],br,bg,bb)+dist(d[j],d[j+1],d[j+2],br,bg,bb); }
    const borderVar=varsum/(2*w);
    if(borderVar>120) return {removed:false};

    const tolSq=tolerance*tolerance;
    const seen=new Uint8Array(w*h);
    const stack=[];
    const push=(x,y)=>{ if(x<0||y<0||x>=w||y>=h) return; const p=y*w+x; if(seen[p]) return; seen[p]=1; stack.push(p); };
    for(let x=0;x<w;x++){ push(x,0); push(x,h-1); }
    for(let y=0;y<h;y++){ push(0,y); push(w-1,y); }

    let cleared=0;
    while(stack.length){
      const p=stack.pop(), x=p%w, y=(p/w)|0, i=p*4;
      if(distSq(d[i],d[i+1],d[i+2],br,bg,bb)>tolSq) continue;
      d[i+3]=0; cleared++;
      push(x+1,y); push(x-1,y); push(x,y+1); push(x,y-1);
    }
    // feather 1px so edges don't alias harshly
    return {removed:cleared>(w*h*0.02), bg:rgbToHex(br,bg,bb), cleared};
  }

  /* ---------------- 3. colour reduction (k-means) ---------------- */
  function quantise(imgData, k){
    const {width:w,height:h,data:d}=imgData;
    // collect opaque pixels
    const pts=[];
    for(let p=0;p<w*h;p++){ const i=p*4; if(d[i+3]>128) pts.push(i); }
    if(!pts.length) throw new Error('That image looks empty once the background was removed. Try lowering the background sensitivity.');

    /* Learn the palette from INTERIOR pixels only. Anti-aliased edge
       pixels are blends of two real colours; if they train the palette
       they get promoted into a thread of their own and every shape ends
       up with a muddy halo around it. Excluding them keeps the palette
       to the colours actually in the logo and gives crisp boundaries. */
    const chDiff=(i,j)=>Math.max(Math.abs(d[i]-d[j]),Math.abs(d[i+1]-d[j+1]),Math.abs(d[i+2]-d[j+2]));
    const interior=[];
    for(let y=0;y<h;y++){
      for(let x=0;x<w;x++){
        const p=y*w+x, i=p*4;
        if(d[i+3]<=128) continue;
        let g=0;
        if(x>0)   g=Math.max(g,chDiff(i,i-4));
        if(x<w-1) g=Math.max(g,chDiff(i,i+4));
        if(y>0)   g=Math.max(g,chDiff(i,i-w*4));
        if(y<h-1) g=Math.max(g,chDiff(i,i+w*4));
        if(g<20) interior.push(i);
      }
    }
    const train = interior.length > Math.max(400, pts.length*0.15) ? interior : pts;

    // sample for speed
    const SAMPLE=12000;
    const step=Math.max(1,Math.floor(train.length/SAMPLE));
    const sample=[];
    for(let s=0;s<train.length;s+=step){ const i=train[s]; sample.push([d[i],d[i+1],d[i+2]]); }

    // k-means++ style seeding
    const cents=[sample[Math.floor(sample.length/2)].slice()];
    while(cents.length<k){
      let best=null,bestD=-1;
      for(let t=0;t<sample.length;t+=3){
        const p=sample[t];
        let m=Infinity;
        for(const c of cents){ const dd=distSq(p[0],p[1],p[2],c[0],c[1],c[2]); if(dd<m)m=dd; }
        if(m>bestD){bestD=m;best=p;}
      }
      if(!best) break;
      cents.push(best.slice());
    }

    for(let iter=0;iter<10;iter++){
      const sum=cents.map(()=>[0,0,0,0]);
      for(const p of sample){
        let bi=0,bd=Infinity;
        for(let c=0;c<cents.length;c++){ const dd=distSq(p[0],p[1],p[2],cents[c][0],cents[c][1],cents[c][2]); if(dd<bd){bd=dd;bi=c;} }
        const s=sum[bi]; s[0]+=p[0]; s[1]+=p[1]; s[2]+=p[2]; s[3]++;
      }
      let moved=0;
      for(let c=0;c<cents.length;c++){
        if(!sum[c][3]) continue;
        const nr=sum[c][0]/sum[c][3], ng=sum[c][1]/sum[c][3], nb=sum[c][2]/sum[c][3];
        moved+=dist(nr,ng,nb,cents[c][0],cents[c][1],cents[c][2]);
        cents[c]=[nr,ng,nb];
      }
      if(moved<2) break;
    }

    // assign every pixel; build index map (-1 = transparent)
    const index=new Int16Array(w*h).fill(-1);
    const counts=new Array(cents.length).fill(0);
    for(let p=0;p<w*h;p++){
      const i=p*4; if(d[i+3]<=128) continue;
      let bi=0,bd=Infinity;
      for(let c=0;c<cents.length;c++){ const dd=distSq(d[i],d[i+1],d[i+2],cents[c][0],cents[c][1],cents[c][2]); if(dd<bd){bd=dd;bi=c;} }
      index[p]=bi; counts[bi]++;
    }

    /* Merge perceptually-identical centroids.
       If the customer asks for more colours than the artwork actually
       contains, k-means splits one colour into two near-identical shades
       which then speckle against each other and the fill looks moth-eaten.
       Genuinely different colours sit 300+ apart, duplicates under ~20. */
    const MERGE_T=60;
    let groups=cents.map((c,i)=>({c:c.slice(), n:counts[i], ids:[i]}));
    for(;;){
      let bi=-1,bj=-1,bd=Infinity;
      for(let i=0;i<groups.length;i++){
        for(let j=i+1;j<groups.length;j++){
          const dd=dist(groups[i].c[0],groups[i].c[1],groups[i].c[2],groups[j].c[0],groups[j].c[1],groups[j].c[2]);
          if(dd<bd){bd=dd;bi=i;bj=j;}
        }
      }
      if(bi<0||bd>=MERGE_T||groups.length<2) break;
      const A=groups[bi], B=groups[bj], tot=(A.n+B.n)||1;
      A.c=[(A.c[0]*A.n+B.c[0]*B.n)/tot,(A.c[1]*A.n+B.c[1]*B.n)/tot,(A.c[2]*A.n+B.c[2]*B.n)/tot];
      A.n=tot; A.ids=A.ids.concat(B.ids);
      groups.splice(bj,1);
    }

    // drop near-empty layers, sort by coverage
    const total=counts.reduce((a,b)=>a+b,0);
    const keep=groups.filter(g=>g.n/total>0.005).sort((a,b)=>b.n-a.n);
    const remap=new Int16Array(cents.length).fill(-1);
    keep.forEach((g,newI)=>g.ids.forEach(id=>remap[id]=newI));
    for(let p=0;p<w*h;p++){ if(index[p]>=0) index[p]=remap[index[p]]; }

    return {
      index, w, h,
      palette: keep.map(g=>({
        hex: rgbToHex(g.c[0],g.c[1],g.c[2]),
        rgb: [Math.round(g.c[0]),Math.round(g.c[1]),Math.round(g.c[2])],
        share: g.n/total
      }))
    };
  }

  /* ---------------- 3b. despeckle ----------------
     Photographed / compressed logos carry grain, which quantises into
     thousands of stray single-pixel specks. Those trace into confetti
     that no one could stitch, so a majority filter cleans them out
     before tracing. Also keeps the vector output fast and tidy. */
  function despeckle(index, w, h, minArea){
    const out=Int16Array.from(index);
    const seen=new Uint8Array(w*h);
    const stack=[], comp=[], nbCount=new Map();

    for(let p0=0;p0<w*h;p0++){
      if(seen[p0]) continue;
      const lbl=out[p0];
      comp.length=0; stack.length=0;
      stack.push(p0); seen[p0]=1;
      while(stack.length){
        const p=stack.pop(); comp.push(p);
        const x=p%w, y=(p/w)|0;
        if(x>0){ const q=p-1; if(!seen[q]&&out[q]===lbl){seen[q]=1;stack.push(q);} }
        if(x<w-1){ const q=p+1; if(!seen[q]&&out[q]===lbl){seen[q]=1;stack.push(q);} }
        if(y>0){ const q=p-w; if(!seen[q]&&out[q]===lbl){seen[q]=1;stack.push(q);} }
        if(y<h-1){ const q=p+w; if(!seen[q]&&out[q]===lbl){seen[q]=1;stack.push(q);} }
      }
      if(comp.length>=minArea) continue;           // real feature — leave it alone

      // speck: absorb it into whichever label surrounds it most
      nbCount.clear();
      for(let i=0;i<comp.length;i++){
        const p=comp[i], x=p%w, y=(p/w)|0;
        if(x>0&&out[p-1]!==lbl)   nbCount.set(out[p-1],  (nbCount.get(out[p-1])||0)+1);
        if(x<w-1&&out[p+1]!==lbl) nbCount.set(out[p+1],  (nbCount.get(out[p+1])||0)+1);
        if(y>0&&out[p-w]!==lbl)   nbCount.set(out[p-w],  (nbCount.get(out[p-w])||0)+1);
        if(y<h-1&&out[p+w]!==lbl) nbCount.set(out[p+w],  (nbCount.get(out[p+w])||0)+1);
      }
      let best=lbl,bn=0;
      nbCount.forEach((c,l2)=>{ if(c>bn){bn=c;best=l2;} });
      if(best!==lbl) for(let i=0;i<comp.length;i++) out[comp[i]]=best;
    }
    return out;
  }

  /* ---------------- 4. vector tracing (sub-pixel marching squares) ----------
     A hard pixel mask can only ever yield axis-aligned staircase steps, and
     a curve drawn through those points inherits every jag — which is what
     made letterforms look wobbly. Instead each colour layer becomes a
     smoothed coverage field, and the outline is extracted as the 0.5
     iso-contour with linear interpolation along each grid edge. That puts
     boundary points at sub-pixel positions, so straight strokes come out
     straight and curves come out genuinely round. */
  function buildField(index,w,h,layer,blurPasses){
    const W=w+2, H=h+2, N=W*H;
    const f=new Float32Array(N), t=new Float32Array(N);
    for(let y=0;y<h;y++){
      const src=y*w, dst=(y+1)*W+1;
      for(let x=0;x<w;x++) if(index[src+x]===layer) f[dst+x]=1;
    }
    for(let p=0;p<blurPasses;p++){
      for(let y=0;y<H;y++){ const r=y*W;
        for(let x=1;x<W-1;x++) t[r+x]=(f[r+x-1]+f[r+x]+f[r+x+1])/3; }
      for(let x=1;x<W-1;x++)
        for(let y=1;y<H-1;y++) f[y*W+x]=(t[(y-1)*W+x]+t[y*W+x]+t[(y+1)*W+x])/3;
    }
    return {f,W,H};
  }

  function traceLayer(index, w, h, layer, scratch, blurPasses){
    const ISO=0.5;
    const {f,W,H}=buildField(index,w,h,layer,blurPasses==null?1:blurPasses);
    const NE=W*H, N=2*NE;
    let {n1,n2,seen,px,py}=scratch;
    if(!n1||n1.length<N){
      n1=scratch.n1=new Int32Array(N); n2=scratch.n2=new Int32Array(N);
      seen=scratch.seen=new Uint8Array(N);
      px=scratch.px=new Float32Array(N); py=scratch.py=new Float32Array(N);
    }
    n1.fill(-1,0,N); n2.fill(-1,0,N); seen.fill(0,0,N);

    const touched=[];
    const link=(a,b)=>{
      if(n1[a]<0){ n1[a]=b; touched.push(a); } else if(n2[a]<0) n2[a]=b;
      if(n1[b]<0){ n1[b]=a; touched.push(b); } else if(n2[b]<0) n2[b]=a;
    };
    const lerp=(va,vb)=>{ const d=vb-va; if(Math.abs(d)<1e-9) return 0.5;
      const t2=(ISO-va)/d; return t2<0?0:(t2>1?1:t2); };

    for(let y=0;y<H-1;y++){
      for(let x=0;x<W-1;x++){
        const i00=y*W+x, i10=i00+1, i01=i00+W, i11=i01+1;
        const tl=f[i00], tr=f[i10], bl=f[i01], br=f[i11];
        const a=tl>=ISO, b=tr>=ISO, c=br>=ISO, e=bl>=ISO;
        const code=(a?8:0)|(b?4:0)|(c?2:0)|(e?1:0);
        if(code===0||code===15) continue;

        const T=i00, B=i01, L=NE+i00, R=NE+i10;
        if(a!==b){ px[T]=x+lerp(tl,tr); py[T]=y; }
        if(b!==c){ px[R]=x+1;           py[R]=y+lerp(tr,br); }
        if(e!==c){ px[B]=x+lerp(bl,br); py[B]=y+1; }
        if(a!==e){ px[L]=x;             py[L]=y+lerp(tl,bl); }

        switch(code){
          case 1: case 14: link(L,B); break;
          case 2: case 13: link(B,R); break;
          case 3: case 12: link(L,R); break;
          case 4: case 11: link(T,R); break;
          case 6: case 9:  link(T,B); break;
          case 7: case 8:  link(L,T); break;
          case 5:  link(T,R); link(L,B); break;
          case 10: link(L,T); link(B,R); break;
        }
      }
    }
    if(!touched.length) return [];

    const loops=[];
    for(let t2=0;t2<touched.length;t2++){
      const k0=touched[t2];
      if(seen[k0]) continue;
      let cur=k0, prev=-1, guard=0;
      const pts=[];
      while(cur>=0 && guard++<400000){
        if(seen[cur]) break;
        seen[cur]=1;
        pts.push([px[cur]-1, py[cur]-1]);      // undo the 1px pad
        const a=n1[cur], b=n2[cur];
        let next=-1;
        if(a>=0 && a!==prev && !seen[a]) next=a;
        else if(b>=0 && b!==prev && !seen[b]) next=b;
        prev=cur; cur=next;
      }
      if(pts.length>=4){ pts.push(pts[0].slice()); loops.push(pts); }
    }
    return loops;
  }

  /* Taubin contour smoothing.
     Blurring the image before thresholding looks smooth but silently eats
     thin artwork: a 1px stroke blurred by a 3x3 kernel peaks near 1/3, below
     the 0.5 iso level, so the stroke disappears. Smoothing the extracted
     outline instead cannot change topology, so hairlines and small text
     survive. Taubin alternates a shrinking and an expanding pass so the
     shape does not creep inwards the way plain Laplacian smoothing does. */
  function smoothLoop(pts, iters, lambda, mu){
    const P=pts.slice(0, pts.length-1);
    const n=P.length;
    if(n<6||iters<=0) return pts;
    const pass=f=>{
      const out=new Array(n);
      for(let i=0;i<n;i++){
        const a=P[(i-1+n)%n], b=P[i], c=P[(i+1)%n];
        out[i]=[ b[0]+f*((a[0]+c[0])*0.5-b[0]), b[1]+f*((a[1]+c[1])*0.5-b[1]) ];
      }
      for(let i=0;i<n;i++) P[i]=out[i];
    };
    for(let k=0;k<iters;k++){ pass(lambda); pass(mu); }
    const res=P.slice(); res.push(P[0].slice());
    return res;
  }

  /* Douglas–Peucker */
  function simplify(pts, eps){
    if(pts.length<4) return pts;
    const sqd=(p,a,b)=>{
      let x=a[0],y=a[1],dx=b[0]-x,dy=b[1]-y;
      if(dx||dy){ const t=((p[0]-x)*dx+(p[1]-y)*dy)/(dx*dx+dy*dy);
        if(t>1){x=b[0];y=b[1];} else if(t>0){x+=dx*t;y+=dy*t;} }
      dx=p[0]-x; dy=p[1]-y; return dx*dx+dy*dy;
    };
    const run=(first,last,out)=>{
      let maxD=0, idx=0;
      for(let i=first+1;i<last;i++){ const dd=sqd(pts[i],pts[first],pts[last]); if(dd>maxD){maxD=dd;idx=i;} }
      if(maxD>eps*eps){ if(idx-first>1) run(first,idx,out); out.push(pts[idx]); if(last-idx>1) run(idx,last,out); }
    };
    const out=[pts[0]];
    run(0,pts.length-1,out);
    out.push(pts[pts.length-1]);
    return out;
  }

  /* Curve fitting.
     Marching squares only ever produces axis-aligned steps, so a polygon
     output makes every circle look faceted. Here each loop is classified
     into corners (sharp direction changes, kept crisp) and smooth runs,
     then emitted as cubic Béziers using Catmull-Rom tangents. That is what
     makes the result read as real vector artwork rather than a tracing. */
  function loopToCurve(pts, cornerDeg){
    const P=pts.slice(0, pts.length-1);          // drop the duplicated closing point
    const n=P.length;
    if(n<3) return '';
    const at=i=>P[((i%n)+n)%n];

    const cosT=Math.cos(cornerDeg*Math.PI/180);
    const corner=new Array(n);
    for(let i=0;i<n;i++){
      const a=at(i-1), b=at(i), c=at(i+1);
      const v1x=b[0]-a[0], v1y=b[1]-a[1], v2x=c[0]-b[0], v2y=c[1]-b[1];
      const l1=Math.hypot(v1x,v1y)||1, l2=Math.hypot(v2x,v2y)||1;
      corner[i]=((v1x*v2x+v1y*v2y)/(l1*l2)) < cosT;
    }

    // Catmull-Rom tangent per vertex; zero at corners so they stay sharp
    const T=new Array(n);
    for(let i=0;i<n;i++){
      if(corner[i]){ T[i]=[0,0]; continue; }
      const a=at(i-1), c=at(i+1);
      T[i]=[(c[0]-a[0])/6,(c[1]-a[1])/6];
    }

    const f=v=>Math.round(v*100)/100;
    let d='M'+f(P[0][0])+' '+f(P[0][1]);
    for(let i=0;i<n;i++){
      const p0=at(i), p1=at(i+1), t0=T[i], t1=T[(i+1)%n];
      if(t0[0]===0&&t0[1]===0&&t1[0]===0&&t1[1]===0){
        d+='L'+f(p1[0])+' '+f(p1[1]);                       // straight edge
      }else{
        d+='C'+f(p0[0]+t0[0])+' '+f(p0[1]+t0[1])+','
             +f(p1[0]-t1[0])+' '+f(p1[1]-t1[1])+','
             +f(p1[0])+' '+f(p1[1]);
      }
    }
    return d+'Z';
  }

  function loopsToPath(loops, eps, cornerDeg, smoothIters){
    let d='';
    for(const raw of loops){
      const p=simplify(smoothLoop(raw, smoothIters==null?3:smoothIters, 0.5, -0.52), eps);
      if(p.length<4) continue;
      d+=loopToCurve(p, cornerDeg==null?70:cornerDeg);
    }
    return d;
  }

  /* ---------------- 5. thread matching ---------------- */
  function matchThread(rgb){
    let best=null,bd=Infinity;
    for(const t of window.THREAD_CHART){
      const [r,g,b]=hexToRgb(t.hex);
      const dd=dist(rgb[0],rgb[1],rgb[2],r,g,b);
      if(dd<bd){bd=dd;best=t;}
    }
    return {thread:best, delta:bd};
  }

  /* ---------------- build SVG ---------------- */
  function buildSVG(layers, w, h, opts){
    opts=opts||{};
    const body=layers.map(l=>`<path d="${l.path}" fill="${opts.mono?opts.mono:l.hex}" fill-rule="evenodd"/>`).join('');
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${w} ${h}" width="${w}" height="${h}" shape-rendering="geometricPrecision">${body}</svg>`;
  }

  /* ---------------- orchestrator ---------------- */
  async function process(src, opts, onStep){
    const t0=performance.now();
    opts=Object.assign({colours:4, tolerance:56, detail:0.8, corner:38, smooth:3}, opts||{});
    const timings={};
    const step=async(label,fn)=>{
      onStep&&onStep(label);
      if(!opts.fast) await new Promise(r=>setTimeout(r,150));
      const t=performance.now(); const out=fn(); timings[label]=Math.round(performance.now()-t);
      return out;
    };

    const img=await loadImage(src);
    const canvas=await step('Reading artwork', ()=>toWorkCanvas(img));
    const ctx=canvas.getContext('2d',{willReadFrequently:true});
    const data=ctx.getImageData(0,0,canvas.width,canvas.height);

    const bg=await step('Removing background', ()=>removeBackground(data, opts.tolerance));
    ctx.putImageData(data,0,0);

    const q=await step('Reducing to thread colours', ()=>quantise(data, opts.colours));

    const minArea=Math.max(6, Math.round(q.w*q.h*0.00006));
    const clean=await step('Cleaning up noise', ()=>despeckle(q.index,q.w,q.h,minArea));

    const layers=await step('Tracing vector outlines', ()=>{
      const out=[], scratch={};
      for(let i=0;i<q.palette.length;i++){
        const loops=traceLayer(clean,q.w,q.h,i,scratch,0);
        const path=loopsToPath(loops, opts.detail, opts.corner, opts.smooth);
        if(path) out.push({hex:q.palette[i].hex, rgb:q.palette[i].rgb, share:q.palette[i].share, path});
      }
      return out;
    });
    if(!layers.length) throw new Error('Could not trace that image. Try a clearer logo or fewer colours.');

    const threads=await step('Matching thread chart', ()=>layers.map(l=>{
      const m=matchThread(l.rgb);
      return { hex:l.hex, share:l.share, thread:m.thread, delta:m.delta };
    }));

    const svg=buildSVG(layers,q.w,q.h);
    return {
      svg, layers, threads, timings,
      width:q.w, height:q.h,
      backgroundRemoved:!!bg.removed,
      ms: Object.values(timings).reduce((a,b)=>a+b,0),   // real processing time
      wallMs: Math.round(performance.now()-t0)             // incl. the staged reveal
    };
  }

  return { process, buildSVG, matchThread, hexToRgb, rgbToHex, lum };
})();
