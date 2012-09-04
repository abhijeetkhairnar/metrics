/********************************************************************************************************
 * 	JQUERY AJAX MULTISELECT TREE
 *  CREATED BY	: RAHUL KATE
 *	UPDATED BY	: AKSHAY SARDAR
 *  FOR 		: GLAM INDIA
 *  VERSION		: 1.0
 *  CREATED ON 	: 02 SEPT 2010
 *  MODEFIED ON : 01 Aug 2012
 *  DESCRIPTION : THIS PLUGIN WAS BUILT TO USE DRAG AND DROP BETWEEN TWO BOXES WITH TREE VIEWS.
 *  			  IT ALSO HAS SORTINGA AND AJAX PAGING BUILT INTO IT. 
 ********************************************************************************************************/

(function($) {
	
	$.widget("ui.ajaxddlist", {
		
		 options: {
			ajax: true,
			source: "",
			records: 8,
			data: "",
			leftSearchVal: "",
			rightSearchVal: "",
			selectedVals: "",
			selectedData: ""
		 },
		 _loadAvailable: function(data, node){
		 	 	var self = this;
	 			$.each(data, function(i,item){
					var children = item.hasChildren?'true':'false';
					var selectable = item.selectable?'true':'false';
					var li = $('<li id="'+self.id+'_'+item.id+'" class="ui-draggable" hasChildren="'+children+'" selectable="'+selectable+'"><div class="ui-widget-content"><a href="javascript:void(0)"><span class="ui-icon ui-icon-plus"></span></a><span class="text">'+item.text+'</span></div></li>').appendTo(node);
					var expanded = item.expanded?'':'ui-helper-hidden';
					var li_helper = $('<li class="'+expanded+'"><ul id="'+self.id+'_'+item.id+'_ul"></ul></li>').appendTo(node);
					self._makeAvailable(li);
					// IF THE CALL IS NOT AJAX AND NODE HAS CHILDREN
					if(children && !self.options.ajax) self._loadAvailable(item.children, item);
				});
		 },
		 _loadSelected: function(){
			 var self = this;
			 var data = self.options.selectedData;
			 var term = self.rightSearch.val();
			 $.each(data, function(i, item){
				 var li = $('<li id="'+self.id+'_'+item.id+'"><div class="ui-widget-content"><a href="javascript:void(0)"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></a><span class="text">'+item.text+'</span><a href="javascript:void(0)" class="right" alt="Remove" title="Remove"><span class="ui-icon ui-icon-minus"></span></a></div></li>');
				 var li_helper = $('<li class="ui-helper-hidden"><ul id="'+self.id+'_'+item.id+'_ul"></ul></li>').appendTo(self.availableList);
				 li.appendTo(self.selectedList);
				 li_helper.appendTo(self.availableList);
				 //self._makeSelected(li);
				 if(term.trim() != ''){
					 var text = li.text().toLowerCase();
					 if(text.indexOf(term) > -1)
						li.removeClass('ui-helper-hidden');
					 else
						li.addClass('ui-helper-hidden');	
				 }
				 //alert('sad');
			 });
		 },
		 _load: function(node, src, vars){
			 	var self = this;
			 	vars += '&search='+self.leftSearch.val();
			 	vars += '&records='+self.limit;
			 	vars += '&page='+self.page;
				/******************* Fetched the right side value while loading the data to avoid duplication of data **********************/
					var rightsideval = "";
						$('#'+self.id+'_right .tree-container #'+self.id+'_ul_selected li').each(function(index) {
							 rightsideval +=$(this).attr('id')+',';
						});	
					vars += '&rightsideval='+rightsideval;				
				/***************************************************************************************************************************/
				
				/******************* Fetched the left side value while loading the data to avoid duplication of data **********************/
					var leftsideval = "";
						$('#'+self.id+'_left .tree-container #'+self.id+'_ul li').each(function(index) {
							if($(this).attr('id') != ''){
								leftsideval +=$(this).attr('id')+',';
							}
						});	
					vars += '&leftsideval='+leftsideval;				
				/***************************************************************************************************************************/				
				self.page += 1;		 	
				// --- Commented as it was always calculating the height,width,offset(top,left and postion) each time the ajax call is mad ---//
			 	//self.preLoader.height(self.element.height()).width(self.element.width()).offset(self.element.offset()).fadeIn('slow');
				// - this only calculate the height and width only and top and left will be 0px. 
					self.preLoader.height(self.element.height()).width(self.element.width()).css('left','0px').css('top','0px').fadeIn('slow');
					
				self.preLoader.fadeIn('slow');
			 	if(self.options.ajax && self.options.source!=''){
				 	$.ajax({ url: src, type: 'POST', data: vars, async: false, dataType: 'json',
				 		success: function(data){
				 			self._loadAvailable(data, node);
					 	}, complete: function(XMLHttpRequest, textStatus){
					 		self.preLoader.fadeOut(3000);
					 	}		
					});
			 	}else{
			 		self._loadAvailable(self.options.data, node);
			 	}
				self._checkRenderedHtml();
				return true;
		 },
		 _checkRenderedHtml: function(){
			/*************************************************************************************************************************/
			// Code to rebuild the miss HTML element to left DDlist to get the right side DDlist data on the correct level and sequence // 
			//													---- START ----														 //
			/*************************************************************************************************************************/
				$('#'+self.id+'_right .tree-container #'+self.id+'_ul_selected li').each(function(index) {					
					var right_li_list = $(this).attr('id').split('_');
					var right_li_list_val = right_li_list[(right_li_list.length-1)];
					var flag_li  = 0;					
					if(right_li_list.length == 2){					
					$('#'+$(this).attr('id')+'_ul').parent().remove();
						$('#'+self.id+'_left .tree-container #'+self.id+'_ul li').each(function(index) {							
							if($(this).attr('id')){
								var left_li_list = $(this).attr('id').split('_');
								if(left_li_list.length == 2){
									var left_li_list_val = left_li_list[(left_li_list.length-1)];
										if(left_li_list_val){
											if(Number(left_li_list_val)>Number(right_li_list_val)){
												if($('#'+self.id+'_'+right_li_list_val+'_ul').html() == null){
													flag_li = 1;													
													$('#'+self.id+'_'+left_li_list_val).before('<li class="ui-helper-hidden"><ul id="'+self.id+'_'+right_li_list_val+'_ul"></ul></li>');
												}
												return false;
											}					
										}
								}
							}else if($(this).attr('class') == 'ui-helper-hidden'){								
								var left_li_list = $(this).children().attr('id').split('_');
								if(left_li_list.length == 3){
									var left_li_list_val = left_li_list[(left_li_list.length-2)];
										if(left_li_list_val){
											if(Number(left_li_list_val)>Number(right_li_list_val)){
												if($('#'+self.id+'_'+right_li_list_val+'_ul').html() == null){
													flag_li = 1;													
													$('#'+self.id+'_'+left_li_list_val+'_ul').parent().before('<li class="ui-helper-hidden"><ul id="'+self.id+'_'+right_li_list_val+'_ul"></ul></li>');
												}
												return false;
											}					
										}
								}
							}							
						});							
						if(flag_li == 0){
							if($('#'+$(this).attr('id')+'_ul').html() == null){								
								 $('<li class="ui-helper-hidden"><ul id="'+$(this).attr('id')+'_ul"></ul></li>').appendTo($('#'+self.id+'_ul'));
							}
						}						
					}else if(right_li_list.length == 4){						
						$('#'+$(this).attr('id')+'_ul').parent().remove();						
						$('#'+self.id+'_left .tree-container #'+self.id+'_ul li ul li').each(function(index) {								
							if($(this).attr('id')){
								var left_li_list = $(this).attr('id').split('_');
								if(left_li_list.length == 4){
									var left_li_list_val = left_li_list[(left_li_list.length-1)];
										if(left_li_list_val){											
											if(Number(left_li_list_val)>Number(right_li_list_val)){												
												if($('#'+self.id+'_'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_'+right_li_list_val+'_ul').html() == null){
													flag_li = 1;													
													$('#'+self.id+'_'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_'+left_li_list_val).before('<li class="ui-helper-hidden"><ul id="'+self.id+'_'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_'+right_li_list_val+'_ul"></ul></li>');
												}
												return false;
											}					
										}
								}
							}else if($(this).attr('class') == 'ui-helper-hidden'){								
								var left_li_list = $(this).children().attr('id').split('_');
								if(left_li_list.length == 5){
									var left_li_list_val = left_li_list[(left_li_list.length-2)];
										if(left_li_list_val){
											if(Number(left_li_list_val)>Number(right_li_list_val)){
												if($('#'+self.id+'_'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_'+right_li_list_val+'_ul').html() == null){
													flag_li = 1;													
													$('#'+self.id+'_'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_'+left_li_list_val+'_ul').parent().before('<li class="ui-helper-hidden"><ul id="'+self.id+'_'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_'+right_li_list_val+'_ul"></ul></li>');													
												}
												return false;
											}					
										}
								}
							}								
						});							
						if(flag_li == 0){							
							if(($('#'+$(this).attr('id')+'_ul').html() == null) && ($('#'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_ul').parent().attr('class') != 'ui-helper-hidden')){								
								$('<li class="ui-helper-hidden"><ul id="'+$(this).attr('id')+'_ul"></ul></li>').appendTo($('#'+self.id+'_'+right_li_list[(right_li_list.length-2)]+'_ul'));								
							}
						}						
					}
				});		
			
			/*************************************************************************************************************************/
			// 													----- END -----														 //
			/*************************************************************************************************************************/				 
		 },
		 _prepare: function(){ 
			 // START PREPARING
			 var self = this;
			 this.id = this.element.attr('id');
			 this.container = this.element;
			 this.startSelection = false;
			 // FOR LAZY LOADING PAGING
			 this.page = 0;
			 this.limit = self.options.records;
			 // KEYCODES 16 FOR SHIFT AND 17 FOR CONTROL
			 $(document).keydown(function(e){
				 if(parseInt(e.keyCode) == 16) self.startSelection = true;
			 }).keyup(function(e){
				 if(parseInt(e.keyCode) == 16) self.startSelection = false;
			 });
			 this.selectionStartLeft = '';
			 this.selectionStartRight = '';
			 // DRAW AND PAINT THE BOXES
			 this.element.addClass('tree');
			 this.selected = $('<input type="hidden" id="'+self.id+'_selected" name="'+self.id+'_selected" value="'+self.options.selectedVals+'"  />').appendTo(this.container);
			 this.selectedData = $('<input type="hidden" id="'+self.id+'_data" name="'+self.id+'_data" value="'+self.options.selectedData+'" />').appendTo(this.container);
			 this.leftBox = $('<div />').addClass('tree-branch available').attr('id',this.id+'_left').appendTo(this.container);
			 this.middleBox = $('<div />').addClass('tree-middle').attr('id',this.id+'_middle').appendTo(this.container);
			 this.rightBox = $('<div />').addClass('tree-branch selected').attr('id',this.id+'_right').appendTo(this.container);
			 this.leftSearch = $('<input name="'+self.id+'_leftSearch" value="'+self.options.leftSearchVal+'" />').addClass('tree-search ui-widget-header ui-corner-all');
			 this.leftHeader = $('<div />').addClass('tree-header ui-widget-header ui-helper-clearfix').append(this.leftSearch).appendTo(this.leftBox);
			 this.leftBody = $('<div />').addClass('tree-container ui-multiselect').appendTo(this.leftBox);
			 this.leftWrapper = $('<div />').addClass('tree-wrapper').appendTo(this.leftBody); 
			 this.rightSearch = $('<input name="'+self.id+'_rightSearch" value="'+self.options.rightSearchVal+'" />').addClass('tree-search ui-widget-header ui-corner-all');
			 this.rightHeader = $('<div />').addClass('tree-header ui-widget-header ui-helper-clearfix').append(this.rightSearch).appendTo(this.rightBox);
			 this.rightBody = $('<div />').addClass('tree-container ui-multiselect').appendTo(this.rightBox);
			 this.sortButtons =  $('<div />').addClass('tree-middle').attr('id',this.id+'_middle').appendTo(this.container);
			 this.availableList = $('<ul id="'+this.id+'_ul"></ul>').appendTo(this.leftWrapper);
			 this.selectedList = $('<ul id="'+this.id+'_ul_selected"></ul>').appendTo(this.rightBody);
			 this.preLoader = $('<div class="ui-loading" style="display:none;"></div>').appendTo(this.container);
			 this.preLoader.height(this.element.height());
			 this.icons = $('<div class="ui-state-default ui-corner-all icon" title="Move Selected"><span class="ui-icon ui-icon-triangle-1-e"></span></div>')
			 				.click(function(){
			 					var rows = self.availableList.find('.ui-selected').not('.ui-helper-hidden');
			 					rows.map(function(){
			 						self._makeSelected($(this));
								});
			 					if(rows.length > 0){
				 					if($('#'+self.id+'_ul').children('li').not('.ui-helper-hidden').length < 8)									
				 						self._load('#'+self.id+'_ul', self.options.source, 'scroll=true&root=source&search='+self.leftSearch.val());			 					
			 					}
			 				})
			 				.hover(function(){ $(this).toggleClass('ui-state-hover'); }).appendTo(this.middleBox);
			 this.icons = $('<div class="ui-state-default ui-corner-all icon" title="Move Selected"><span class="ui-icon ui-icon-triangle-1-w"></span></div>')
			 				.click(function(){
			 					var rows = self.selectedList.find('.ui-selected').not('.ui-helper-hidden');
			 					rows.map(function(){
			 						self._makeAvailable($(this));
								});
			 				})
			 				.hover(function(){ $(this).toggleClass('ui-state-hover'); }).appendTo(this.middleBox);
			 this.iconsu = $('<div class="ui-state-default ui-corner-all icon" title="Move Up"><span class="ui-icon ui-icon-triangle-1-n"></span></div>')
							.click(function(){
								var rows = self.selectedList.find('.ui-selected').not('.ui-helper-hidden');
								rows.map(function(){
									$(this).insertBefore($(this).prev());
								});
								if(rows.length > 0) self._updateSelection();
							})
							.hover(function(){ $(this).toggleClass('ui-state-hover'); }).appendTo(this.sortButtons);
			 this.iconsd = $('<div class="ui-state-default ui-corner-all icon" title="Move Down"><span class="ui-icon ui-icon-triangle-1-s"></span></div>')
							.click(function(){
								var rows = self.selectedList.find('.ui-selected').not('.ui-helper-hidden');
								rows.map(function(){
									$(this).insertAfter($(this).next());
								});
								if(rows.length > 0) self._updateSelection();
							})
							.hover(function(){ $(this).toggleClass('ui-state-hover'); }).appendTo(this.sortButtons);
			 this.actions = $('<div class="right"><a href="javascript:void(0)" class="all" title="Select All"></a><a href="javascript:void(0)" class="invert" title="Invert Selection"></a></div>').appendTo(self.leftHeader);
			 this.leftHeader.find('.all').click(function(){
				 self.availableList.find('li.ui-draggable').not('.ui-helper-hidden').addClass('ui-selected');
				 $('#'+self.id+'_left .tree-container #'+self.id+'_ul li ul').find('li').removeClass('ui-selected');
			 });
			 this.leftHeader.find('.invert').click(function(){
				 self.availableList.find('li.ui-draggable').toggleClass('ui-selected');
			 });
			 this.actions = $('<div class="right"><a href="javascript:void(0)" class="all" title="Select All"></a><a href="javascript:void(0)" class="invert" title="Invert Selection"></a></div>').appendTo(self.rightHeader);			 
			 this.rightHeader.find('.all').click(function(){
				 self.selectedList.find('li').addClass('ui-selected');
			 });
			 this.rightHeader.find('.invert').click(function(){
				 self.selectedList.find('li').toggleClass('ui-selected');
			 });
			 // LAZY LOAD ON SCROLL
			 var scroll = false;
			 this.leftBody.scroll(function() { 
				 // STATIC DATA THEN
				 if(!self.options.ajax) return false;
				 // ELSE LOAD JSON DATA ON SCROLL WITH AJAX
				 if($(this).scrollTop() >= (self.leftWrapper.height() - $(this).height()) && scroll == false){
						scroll = true;
						if(self._load(self.availableList, self.options.source, 'root=source'))
							scroll = false;
				 }
			 });
			 // LOAD AGAIN WHEN LEFT SEARCH IS CALLED
			 this.leftSearch.bind('keyup',function(){ 
				 // HIDE SHOW STATIC DATA
				 if(!self.options.ajax){
					 var term = self.leftSearch.val().toLowerCase();
						var nodes = self.availableList.children('li');
						nodes.map(function(){
							text = $(this).text().toLowerCase();
							if(text.indexOf(term) > -1)
								$(this).removeClass('ui-helper-hidden');
							else
								$(this).addClass('ui-helper-hidden');						
						});
				 }else{
					if(self.leftSearch.val().length >=3 || self.leftSearch.val().length == 0 ){
						 // SET PAGE TO - 0
						 self.page = 0;
						 self.availableList.children().remove();
							 if(self.availableList.children().length == 0){
								self._load(self.availableList, self.options.source, '');
							}
					}
				 }
			 });
			 // WHEN RIGHT SEARCH IS CALLED
			 this.rightSearch.bind('keyup',function(){
				if($(this).value == '')
					self.selectedList.find('.ui-helper-hidden').removeClass('ui-helper-hidden');
				else{
					var term = self.rightSearch.val().toLowerCase();
					var nodes = self.selectedList.children('li');
					nodes.map(function(){
						text = $(this).text().toLowerCase();
						if(text.indexOf(term) > -1)
							$(this).removeClass('ui-helper-hidden');
						else
							$(this).addClass('ui-helper-hidden');						
					});
				}
			 });
			 this.selectedList.sortable({ distance: 20 }).disableSelection();
			 // ON DROP : SELECT OR SORT BOTH CASES
			 this.rightBody.droppable({
				 accept: 'li',
				 drop: function(event, ui){
				 	item = ui.draggable;
				 	if(item.hasClass('ui-draggable'))
				 		self._makeSelected(item);
				 	else
				 		item.toggleClass('ui-selected');
			 		setTimeout(function() { self._updateSelection(); }, 1);
 					if($('#'+self.id+'_ul').children('li').not('.ui-helper-hidden').length < 8)
 						self._load('#'+self.id+'_ul', self.options.source, 'root=source');
			 	 }
			 });			 
			 this._loadSelected();
			 // IF AJAX IS ENABLED THEN LOAD
			 if(self.options.ajax) this._load(this.availableList, this.options.source, 'root=source');
			 this.selectionStartLeft = '';
		 },
		 _create: function(){ 
			 this._prepare();
		 },
		 destroy: function() {
			 $.Widget.prototype.destroy.apply(this, arguments); 
		 },
		 _cloneWithData: function(clonee) {
				var clone = clonee.clone();
				clone.data('optionLink', clonee.data('optionLink'));
				clone.data('idx', clonee.data('idx'));
				return clone;
		},
		/* MAKE SELECTED */
		 _makeSelected: function(item){
			var self = this;
			// THE TEXT INSIDE THE ITEM
			var iText = item.find('.text').text();
			var pItem = item;
			while(pItem.parent().parent().prev().attr('id')){
				iText = pItem.parent().parent().prev().find('.text').text() + ' >> '+iText;
				pItem = pItem.parent().parent().prev();
			}
			item.find('.text').text(iText);
			// THE PLUS AND MINUS ICON TO DRAG ICON
			if(item.find('.ui-icon').length > 0){
				item.find('.ui-icon-plus').removeClass('ui-icon-plus').addClass('ui-icon-arrowthick-2-n-s');
				item.find('.ui-icon-minus').removeClass('ui-icon-minus').addClass('ui-icon-arrowthick-2-n-s');
				item.find('.ui-icon-arrowthick-2-e-w').removeClass('ui-icon-arrowthick-2-e-w').addClass('ui-icon-arrowthick-2-n-s');
				item.find('.ui-icon').parent().unbind('click');
				item.appendTo(self.selectedList);
			}
		 	if(item.children('div').children('.right').length < 1 ){
			 	var remove = $('<a href="javascript:void(0)" class="right" alt="Remove" title="Remove"><span class="ui-icon ui-icon-minus"></span></a>')
			 	.unbind('click').click(function(){
			 		self._makeAvailable($(this).parent().parent());
			 	})
			 	.appendTo(item.find('div'));
		 	};		
			// --- toggle the class as it create problem when we get the same parent back to left side  and try to expand the child list --- //
			if($('#'+item.attr('id')+'_ul').parent().attr('class')==''){
				$('#'+item.attr('id')+'_ul').parent().toggleClass('ui-helper-hidden');
			}
			//	--- END --- //
			item.removeClass('ui-draggable').removeClass('ui-selected').draggable({ disabled: true }).removeClass('ui-draggable-disabled').removeClass('ui-state-disabled')
			.unbind('dblclick').dblclick(function(){
				self._makeAvailable($(this));
			}).unbind('click').click(function(){
				if(self.startSelection){
					$(this).toggleClass('ui-selected');	
					if(self.selectionStartRight.trim() == '')
						return false;
					var thisId = $(this).attr('id');
					var thatId = self.selectionStartRight;
					var selRows = self.selectedList.find('li');
					var selStart = false;
					selRows.map(function(){
						var cid = $(this).attr('id');
						if((cid == thisId || cid == thatId) && $(this).hasClass('ui-selected') && !selStart)
							selStart = true;
						else if((cid == thisId || cid == thatId) && $(this).hasClass('ui-selected') && selStart)
							selStart = false;
						if(selStart) $(this).addClass('ui-selected');
					});
				}else{
					$(this).toggleClass('ui-selected');
					self.selectionStartRight = item.attr('id');
				}
			});
			// REMOVE IF ANY CHILDS OF THIS ELEMENT ARE PRESENT
			rows = self.selectedList.find('li'); 
			rows.map(function(){
				if($(this).attr('id').indexOf(item.attr('id')+'_') > -1){
					$(this).remove();
				}
				self.availableList.find('#'+item.attr('id')+'_ul').children('li').remove();
			});
			/*********************************** START ******************************************/
			/*	Added the below code so that the after double click if the list is less than 8	*/
			/*	the ajax call is send to the source file to fetch the data and show in DD		*/	
			/************************************************************************************/					
				if($('#'+self.id+'_ul').children('li').not('.ui-helper-hidden').length < 8){
					self._load('#'+self.id+'_ul', self.options.source, 'scroll=true&root=source&search='+self.leftSearch.val());			 					
				}
			/************************************ END *******************************************/
			// UPDATE THE SELECTION
			setTimeout(function() { self._updateSelection(); }, 1);			
		},
		_makeAvailable: function(item){
			var self = this;
			var iText = item.find('.text').text();
			iText = iText.split(' >> ');
			item.find('.text').text(iText[iText.length-1]);
			if($('#'+item.attr('id')+'_ul').length < 1){
				item.remove();
				return true;
			}
			if(item.attr('selectable')=="true"){	
				item.removeClass('ui-helper-hidden').addClass('ui-draggable').draggable({
					distance: 20,
					disabled: false,
		 			revert: 'invalid',
					connectToSortable: self.selectedList,
					containment: self.container,
					appendTo: self.selectedList,
					helper: function() {
	  					var selectedItem = self._cloneWithData($(this)).width($(this).width());
	  					selectedItem.width($(this).width());
	  					return selectedItem;
	  				},
					start: function(){
	  					$(this).addClass('ui-helper-hidden');
	  				},
	  				stop: function(){
	  					$(this).removeClass('ui-helper-hidden');
	  				}
		 		}).unbind('dblclick').dblclick(function(){
					self._makeSelected($(this));
				}).unbind('click').click(function(){
					if(self.startSelection){
						$(this).toggleClass('ui-selected');
						if(self.selectionStartLeft.trim() == '')
							return false;
						var thisId = $(this).attr('id');
						var thatId = self.selectionStartLeft;
						var selRows = self.availableList.find('li');
						var selStart = false;
						selRows.map(function(){
							var cid = $(this).attr('id');
							if((cid == thisId || cid == thatId) && $(this).hasClass('ui-selected') && !selStart)
								selStart = true;
							else if((cid == thisId || cid == thatId) && $(this).hasClass('ui-selected') && selStart)
								selStart = false;
							if(selStart) $(this).addClass('ui-selected');
						});
					}else{
						$(this).toggleClass('ui-selected');
						self.selectionStartLeft = $(this).attr('id');
					}
					$(this).parent().parent().prev().removeClass('ui-selected');
					$(this).next().children('ul').find('li').removeClass('ui-selected');				
				});
			}
			// DO THE OTHER CSS WORK
	 		item.find('.ui-icon').removeClass('ui-icon-arrowthick-2-n-s').addClass('ui-icon-plus');
			if(item.attr('haschildren')=="false") 
				item.find('.ui-icon').removeClass('ui-icon-plus').addClass('ui-icon-arrowthick-2-e-w');
	 		if( $('#'+item.attr('id')+'_ul').length < 1 ){
	 			var li = $('<li class="ui-helper-hidden" id="'+item.attr('id')+'_ul"><ul></ul></li>')
	 			.appendTo(self.availableList);
	 		}
	 		item.appendTo(self.availableList);
	 		if($('#'+item.attr('id')+'_ul').length > 0){			
	 			item.insertBefore($('#'+item.attr('id')+'_ul').parent());	
	 		}
			// DO THE WORK FOR ICON CLICK
	 		item.find('.ui-icon-minus').parent().remove();
	 		item.find('.ui-icon-plus').parent().unbind('click').click(function(){ 	
	 			if(item.attr('selectable')=="true") 
	 				$(this).parent().parent().toggleClass('ui-selected');
				$(this).children('.ui-icon').toggleClass('ui-icon-minus');
				$(this).parent().parent().next().toggleClass('ui-helper-hidden');
				if(self.options.ajax){
					if($(this).parent().parent().next().find('ul').children('li').length < 1){
						/************** Added the code that decrement page value by one when expanded as it is incremented each time. *************/
							self.page -= 1;
						/*********************** END **************************************/
						self._load($(this).parent().parent().next().children('ul'), self.options.source, 'root='+$(this).parent().parent().attr('id'));
					}
				}
			});
	 		// REMOVE SELECTION IF ITS SELECTED
	 		setTimeout(function() { item.removeClass('ui-selected'); } ,1);	
	 		// UPDATE THE SELECTION
	 		setTimeout(function() { self._updateSelection(); }, 1);
		}, /* MAKE AVAILABLE */
		_updateSelection: function(){
			var self = this;
			var selections = '';
			var rows = self.selectedList.find('li');
			selectionData = '[';
			rows.map(function(){
				/*
				//---- 	akshay
				//alert($(this).attr('id'));
							var selected_item_val = $(this).attr('id').split('_');
							var selected_item_val_id = '';
							if(selected_item_val.length == 2){	
								selected_item_val_id = selected_item_val[(selected_item_val.length-1)];
							}else if(selected_item_val.length == 4){	
								selected_item_val_id = selected_item_val[(selected_item_val.length-2)]+'_'+selected_item_val[(selected_item_val.length-1)];
							}	
				selections += selected_item_val_id+',';
				selectionData += '{ \'text\': \''+$(this).text()+'\', \'id\': \''+selected_item_val_id+'\' },';		
				*/		
				selections += $(this).attr('id')+',';
				selectionData += '{ \'text\': \''+$(this).text()+'\', \'id\': \''+$(this).attr('id')+'\' },';
			});
			selectionData = selectionData.substring(0, selectionData.length-1);
			if(selectionData){
				selectionData += ']';
			}
			$('#'+self.id+'_data').val(selectionData);
			selections = selections.substring(0, selections.length -1);
			self.selected.val(selections);
		}
	});
})(jQuery); 