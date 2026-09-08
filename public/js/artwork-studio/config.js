/* ============================================================
   Owner-editable settings.
   Prices and packages live here so they can be changed without
   touching any of the tool logic.
   ============================================================ */
window.STUDIO_CONFIG = {

  /* Design-from-scratch packages. Prices shown ex VAT.
     Chelsea to confirm final figures. */
  packages: [
    { id:'text',  name:'Text Logo',        price:45,
      blurb:'Your name set in professional type, ready to stitch or print.',
      includes:['Custom typography','2 concepts to choose from','1 round of changes','All final files'] },

    { id:'icon',  name:'Logo with Icon',   price:95, popular:true,
      blurb:'A symbol plus your name, the usual choice for trades and businesses.',
      includes:['Custom icon and typography','3 concepts to choose from','2 rounds of changes','All final files','Light and dark versions'] },

    { id:'crest', name:'Crest or Badge',   price:145,
      blurb:'A full badge for clubs, teams and schools.',
      includes:['Full crest or badge design','3 concepts to choose from','3 rounds of changes','All final files','Light and dark versions','Simplified small-size version'] }
  ],

  turnaround: '3 to 5 working days',
  ownerEmail: 'customerservice@threadsembroideryprint.co.uk',

  /* Details asked for before the finished files download. Free tool,
     but it turns every use into a contact Chelsea can follow up. */
  captureFields: true
};
