/* ============================================================
   Garment placement config for the customiser.
   The stage shows the REAL product photo. For each placement we
   store a calibrated position on that photo (center x,y and width,
   as % of the square stage), tuned per garment type. Only the
   SELECTED placement's logo is drawn — so the photo stays clean.
   ============================================================ */
(function(){
  const T=window.THREADS, C=T.CONFIG;

  /* extra placement prices (admin-editable) */
  const extra=[
    {id:'cap_front',   label:'Cap Front',     method:'both', price:3.50, sizes:['50mm','60mm','70mm']},
    {id:'cap_back',    label:'Cap Back',      method:'both', price:3.50, sizes:['50mm','60mm']},
    {id:'bag_front',   label:'Bag Front',     method:'both', price:6.00, sizes:['150mm','200mm','A4']},
    {id:'apron_chest', label:'Bib / Chest',   method:'both', price:3.50, sizes:['70mm','80mm','100mm']},
    {id:'apron_pocket',label:'Pocket / Waist',method:'both', price:6.00, sizes:['150mm','200mm']},
    {id:'leg',         label:'Leg',           method:'both', price:3.50, sizes:['80mm','100mm']},
    {id:'item_front',  label:'Front',         method:'both', price:3.50, sizes:['80mm','100mm','150mm']}
  ];
  extra.forEach(p=>{ if(!C.placements.find(x=>x.id===p.id)) C.placements.push(p); });

  /* calibrated positions (center x,y and logo width, % of square stage) */
  const chestShort={left_chest:{x:38,y:31,w:12},right_chest:{x:62,y:31,w:12},centre_chest:{x:50,y:45,w:22}};
  const slvShort={left_sleeve:{x:22,y:31,w:11},right_sleeve:{x:78,y:31,w:11}};
  const chestLong={left_chest:{x:38,y:35,w:12},right_chest:{x:62,y:35,w:12},centre_chest:{x:50,y:47,w:22}};
  const slvLong={left_sleeve:{x:20,y:57,w:11},right_sleeve:{x:80,y:57,w:11}};
  const backShort={back_print:{x:50,y:42,w:34},back_embroidery:{x:50,y:30,w:20}};
  const backLong={back_print:{x:50,y:44,w:32},back_embroidery:{x:50,y:32,w:20}};

  const TPL={
    shortTop:{label:'T-Shirt', zones:{front:Object.assign({},chestShort,slvShort), back:Object.assign({},backShort,slvShort)}},
    longTop:{label:'Top', zones:{front:Object.assign({},chestLong,slvLong), back:Object.assign({},backLong,slvLong)}},
    gilet:{label:'Gilet', zones:{front:{left_chest:{x:38,y:40,w:12},right_chest:{x:62,y:40,w:12},centre_chest:{x:50,y:44,w:20}}, back:{back_print:{x:50,y:42,w:30},back_embroidery:{x:50,y:32,w:18}}}},
    cap:{label:'Cap', zones:{front:{cap_front:{x:50,y:50,w:24}}, back:{cap_back:{x:50,y:52,w:20}}}},
    bag:{label:'Bag', zones:{front:{bag_front:{x:50,y:60,w:32}}}},
    apron:{label:'Apron', zones:{front:{apron_chest:{x:42,y:40,w:16},apron_pocket:{x:40,y:56,w:26}}}},
    bottoms:{label:'Trousers', zones:{front:{leg:{x:40,y:55,w:12}}}},
    shorts:{label:'Shorts', zones:{front:{leg:{x:40,y:42,w:12}}}},
    generic:{label:'Item', zones:{front:{item_front:{x:50,y:46,w:30}}}}
  };

  const TYPE={
    'T-Shirt':'shortTop','Polo':'shortTop',
    'Hood':'longTop','Sweatshirt':'longTop','Jumper':'longTop','Cardigan':'longTop','Blouse':'longTop',
    'Shirt':'longTop','Jacket':'longTop','Soft Shell':'longTop','Fleece':'longTop',
    'Gilet':'gilet','Headwear':'cap','Bag':'bag','Apron':'apron',
    'Trousers':'bottoms','Shorts':'shorts','Scarf':'generic','Decoration Supplies':'generic'
  };

  T.TEMPLATES=TPL;
  T.templateFor=function(product){ return TPL[TYPE[product&&product.type]||'shortTop']||TPL.shortTop; };
  T.templatePlacementIds=function(tpl){
    const ids=new Set();
    ['front','back'].forEach(v=>{ if(tpl.zones[v]) Object.keys(tpl.zones[v]).forEach(id=>ids.add(id)); });
    return [...ids];
  };
  /* derive the back-photo URL from a front image (Full Collection) */
  T.backPhoto=function(front){ return /FRONT/i.test(front)?front.replace(/FRONT/g,'BACK').replace(/Front/g,'Back'):null; };
})();
