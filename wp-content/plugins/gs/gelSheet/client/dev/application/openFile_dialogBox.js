/*  Gelsheet Project, version 0.0.1 (Pre-alpha)
 *  Copyright (c) 2008 - Ignacio Vazquez, Fernando Rodriguez, Juan Pedro del Campo
 *
 *  Ignacio "Pepe" Vazquez <elpepe22@users.sourceforge.net>
 *  Fernando "Palillo" Rodriguez <fernandor@users.sourceforge.net>
 *  Juan Pedro "Perico" del Campo <pericodc@users.sourceforge.net>
 *
 *  Gelsheet is free distributable under the terms of an GPL license.
 *  For details see: http://www.gnu.org/copyleft/gpl.html
 *
 */
function createOpenFileDialog() {
	var url = 'server/?c=Spreadsheet&m=listBooks';
	var bookid = undefined ;
	
	var div = document.createElement('div');
	div.id = 'loadBook';
	div.style.position = "absolute";
	div.style.top = "50px";
	document.body.appendChild(div) ;

	var win;
    // create the Data Store
    var store = new Ext.data.Store({
        // load using HTTP
        url: url,
        reader: new Ext.data.XmlReader({
               record: 'Item',
               id: 'BookId',
               totalRecords: '@total'
           }, [
               {name: 'Name', mapping: 'ItemAttributes > Name'},
               'CreationDate',
               'BookId'
           ])
    });
    // create the grid
    var grid = new Ext.grid.GridPanel({
        store: store,
        columns: [
                  
			{header: "Id", width: 40, dataIndex: 'BookId', sortable: true},
			{header: "CreationDate", width: 100, dataIndex: 'CreationDate', sortable: true}
                  
      ],
		sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
		viewConfig: {
			forceFit: true
		},
        height:210,
		split: true,
		region: 'north'
    });

	
    
	// define a template to use for the detail view
	var bookTplMarkup = [
		'Name: <b>{Name}</b><br>',
		'BookId: <b>{BookId}</b><br>',
		'Users: <i>No data available.</i><br>'
	];
	var bookTpl = new Ext.Template(bookTplMarkup);

	

	var ct = new Ext.Panel({
		renderTo: Ext.getBody(),//'loadBook',
		frame: true,
		title: 'Book List',
		//width: 540,
		//height: 400,
		layout: 'border',
		items: [
			grid,
			{
				id: 'detailPanel',
				region: 'center',
				bodyStyle: {
					background: '#ffffff',
					padding: '7px'
				},
				html: 'Please select a book to see additional details.'
			}
		]
	});
	
	if (!win) win = new Ext.Window({
		title		: 'Open Book:',
        applyTo     : 'loadBook',
        layout      : 'fit',
        width       : 250,
        height      : 300,
        closeAction :'close',
        plain       : true,
        modal 		: true, // por que no anda?
        
        items		: ct, //pepe
        
        buttons: [{
            text     : 'Open',
            handler  : function(){
        		if (bookid == undefined) 
        			Ext.MessageBox.alert("Status","You must select a book") ;
        		else {
        			CommManager.loadBook(bookid,window.application.bookLoaded);
	        		ct.hide();
	                win.close();
        		}    
        	}
        },{
            text     : 'Cancel',
            handler  : function(){
        		ct.hide();
                win.close();
            }
        }]
    });


	grid.getSelectionModel().on('rowselect', function(sm, rowIdx, r) {
		var detailPanel = Ext.getCmp('detailPanel');
		bookTpl.overwrite(detailPanel.body, r.data);
		bookid = r.data.BookId ;
	});
	
	div.show = function(){
		win.show();
		ct.show();
	}
    store.load();
   
    return div;
}
