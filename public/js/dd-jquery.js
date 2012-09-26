/******************************************************
*	FileName 	 : dd-jquery.js
*	Created By 	 : Aksahy Sardar
*	Created Date : 05 Sept 2012
*	Description	 : DD widgets
*	Version 	 : 1.0                  
******************************************************/

(function($, undefined) {
  $.widget('ui.dd', {
		options: {
			source: "",
			limit: 10,
			pagination: true,
			rightSideData: ""
		},
		_makeSelected: function(){
			var self = this;
			$('#'+self.id+'_right_ul li').each(function(){
				$('#'+self.id+'_left_ul li.'+$(this).attr('class')+' .'+$('.'+$(this).attr('class')+' input[type=hidden]').attr('class')).attr('checked',true);
				$('#'+self.id+'_left_ul li.'+$(this).attr('class')).css('background-color','#FF9900');
			});
			
		},
		_loadData: function(data,pid){
			var self = this;
			if(!pid){
				$.each(data, function(i,item) { 
					var expandChild = "";
					var hideParentchk = "";
					var childrenHtml = "";
					if(item.children){
						expandChild = '<span class="ui-icon ui-icon-plus"></span>';
						childrenHtml = '<ul class="'+self.id+'_left_ul_li_'+item.id+'_ul"></ul>';
						hideParentchk = 'style="display:none;"';
					}
					
				  	$('#'+self.id+'_left_ul').append('<li class="'+self.id+'_left_ul_li_'+item.id+'">'+expandChild+'<span class="chk_span"><input type="checkbox" name="'+self.id+'_left_chk[]" value="'+item.id+'" class="'+self.id+'_chk" '+hideParentchk+' />'+item.text+'</span>'+childrenHtml+'</li>');
				});
			}else if(pid){
				var disable_chk_chk = "";
				$.each(data, function(i,item) { 
					$('.'+self.id+'_left_ul_li_'+pid+'_ul').append('<li class="'+self.id+'_left_ul_li_'+pid+'_ul_li_'+item.id +'"><span class="chk_chk_span"><input type="checkbox" name="'+self.id+'_left_chk['+pid+'][]" value="'+item.id+'" class="'+self.id+'_chk_chk" />'+item.text+'</span></li>');
				});	
			}
			self._makeSelected();
		},
		_load: function(pid){	
			var self = this;
				$.ajax({url: self.options.source,type: 'POST',data: {limit : self.options.limit , page : self.page++ , pid : pid , searchval : self.leftSearch , pagination : self.options.pagination },dataType: 'json',					
						beforeSend: function() { $('#'+self.id+'_warpper .ui-loading').show(); },
						complete: function(){ $('#'+self.id+'_warpper .ui-loading').hide(); },						
						success: function(data){ self._loadData(data,pid); }	
				});	
			return true;
		},
		_prepare: function(){
				var self = this;
				this.id = this.element.attr('id');
				this.container = this.element;
				this.container.append('<div id="'+self.id+'_warpper" class="dd-warpper-div"></div>');
				$('#'+self.id+'_warpper').append('<div class="dd-warpper-left"><div class="dd-left-search-div"><input type="text" name="'+self.id+'_left_search" value="" id="'+self.id+'_left_search" class="dd-left-search"></div><div id="'+self.id+'_left" class="dd-left-div"><ul id="'+self.id+'_left_ul"></ul></div></div><div id="'+self.id+'_middle" class="dd-middle-div"></div><div class="dd-warpper-right"><div class="dd-right-search-div"><input type="text" name="'+self.id+'_right_search" value="" id="'+self.id+'_right_search" class="dd-right-search"></div><div id="'+self.id+'_right" class="dd-right-div"><ul id="'+self.id+'_right_ul"></ul></div></div><div class="ui-loading" style="display:none;"></div>');	
				
				if(self.options.rightSideData){
					var data = self.options.rightSideData;
					$.each(data, function(i,item) {						
						if(item.parentid != 0 || item.parentid == null){
							$('#'+self.id+'_right_ul').append('<li class="'+self.id+'_left_ul_li_'+item.parentid+'_ul_li_'+item.id+'"><input type="hidden" name="'+self.id+'['+item.parentid+'][]" value="'+item.id+'" class="'+self.id+'_chk_chk"/>'+item.text+'<span class="ui-icon ui-icon-close"></span></li>');							
						}else{
							$('#'+self.id+'_right_ul').append('<li class="'+self.id+'_left_ul_li_'+item.id+'"><input type="hidden" name="'+self.id+'[]" value="'+item.id+'" class="'+self.id+'_chk" />'+item.text+'<span class="ui-icon ui-icon-close"></span></li>');
						}
					});
				}

				
				if(self.options.pagination){
					 // LAZY LOAD ON SCROLL
					 var scroll = false;
					 $('#'+self.id+'_left').scroll(function() { 
							if($(this).scrollTop() >= ( $('#'+self.id+'_left_ul').height() - $(this).height()) && scroll == false){
								scroll = true;
								if(self._load(0)){ scroll = false; }
							}
					 });	
				}
				self.page = 0;
				self._load(0);
				
				$('#'+self.id+'_left .'+self.id+'_chk').live("click", function() {
					var self_chk = this
					if ($(this).is(':checked')) {
						$('#'+self.id+'_right_ul').append('<li class="'+$(this).parent().parent().attr('class')+'"><input type="hidden" name="'+self.id+'[]" value="'+$(self_chk).val()+'" class="'+self.id+'_chk" />'+$(this).parent().text()+'<span class="ui-icon ui-icon-close"></span></li>');
						$(this).parent().parent().css('background-color','#FF9900');						
						/*************************************************************/
						if($('#'+self.id+'_left_ul li ul.'+$(this).parent().parent().attr('class')+'_ul').has("li").length){
							$('#'+self.id+'_left_ul li ul.'+$(this).parent().parent().attr('class')+'_ul li input[type=checkbox]:checked').each(function(){
								$(this).attr('checked',false);
								//$(this).parent().parent().css('background-color','#FFF');
								$(this).parent().parent().css('background-color','#FFEEDD');
								$('#'+self.id+'_right_ul li.'+$(this).parent().parent().attr('class')).remove();
							});
						}
						/*************************************************************/												
					} else {
						$('#'+self.id+'_right_ul li.'+$(this).parent().parent().attr('class')).remove();
						//$(this).parent().parent().css('background-color','#FFF');
						$(this).parent().parent().css('background-color','#FFEEDD');
					} 					
 				});

				$('#'+self.id+'_left .'+self.id+'_chk_chk').live("click", function() {	
					var self_chk_chk = this
					if ($(this).is(':checked')) {				
						var parent_id = $('.'+$(this).parent().parent().parent().parent().attr('class')+' span.chk_span input[type=checkbox]').val();
						var parent	= $('.'+$(this).parent().parent().parent().parent().attr('class')+' span.chk_span').text();
						var child	= $(this).parent().text();
						if($('.'+$(this).parent().parent().parent().parent().attr('class')+' span.chk_span input[type=checkbox]').is(':checked')){
							$('.'+$(this).parent().parent().parent().parent().attr('class')+' span.chk_span input[type=checkbox]').attr('checked',false);
							//$('.'+$(this).parent().parent().parent().parent().attr('class')).css('background-color','#FFF');
							$('.'+$(this).parent().parent().parent().parent().attr('class')).css('background-color','#FFEEDD');
							$('#'+self.id+'_right_ul li.'+$(this).parent().parent().parent().parent().attr('class')).remove();
						}
						$('#'+self.id+'_right_ul').append('<li class="'+$(this).parent().parent().attr('class')+'"><input type="hidden" name="'+self.id+'['+parent_id+'][]" value="'+$(self_chk_chk).val()+'" class="'+self.id+'_chk_chk"/>'+parent+' >> '+child+'<span class="ui-icon ui-icon-close"></span></li>');
						$(this).parent().parent().css('background-color','#FF9900');
					} else {
						$('#'+self.id+'_right_ul li.'+$(this).parent().parent().attr('class')).remove();
						//$(this).parent().parent().css('background-color','#FFF');
						$(this).parent().parent().css('background-color','#FFEEDD');
					} 					
 				});	

				
				$('#'+self.id+'_left .ui-icon').live("click", function() {		
					if($(this).hasClass('ui-icon-plus')){
						$(this).removeClass('ui-icon-plus').addClass('ui-icon-minus');						
						if($('.'+$(this).parent().attr('class')+'_ul').has("li").length){
							$('.'+$(this).parent().attr('class')+'_ul').show();								
						}else{						
							var chk_pid = $('.'+$(this).parent().attr('class')+' span .'+self.id+'_chk').val();
							self._load(chk_pid);
						}
					}else if($(this).hasClass('ui-icon-minus')){
						$(this).removeClass('ui-icon-minus').addClass('ui-icon-plus');
						$('.'+$(this).parent().attr('class')+'_ul').hide();
					}	
				});	
				
				$('#'+self.id+'_right #'+self.id+'_right_ul li span.ui-icon-close').live("click", function() {
					  
					   $(this).parent().remove();
					   $('#'+self.id+'_left_ul li.'+$(this).parent().attr('class')+' input[type=checkbox]').attr('checked',false);
					   $('#'+self.id+'_left_ul li.'+$(this).parent().attr('class')).css('background-color','#FFF');					   
				});
				
				$('#'+self.id+' .dd-left-search').live("keyup", function() {
					 self.leftSearch = $.trim($(this).val().toLowerCase());
					 if(self.leftSearch.length >= 2 || self.leftSearch.length == 0){
						$('#'+self.id+'_left_ul li').remove();
						self.page = 0;
						self._load(0);
					 }
				});				
				
				$('#'+self.id+' .dd-right-search').live("keyup", function() {
					self.rightSearch = $.trim($(this).val().toLowerCase());
					$('#'+self.id+'_right_ul li').each(function(){
							text = $(this).text().toLowerCase();
							if(text.indexOf(self.rightSearch) > -1)
								$(this).show();
							else
								$(this).hide();					
						});								 
				});		
				
				/*********** CSS li:hovers related - START ***********/
				$('#'+self.id+'_left ul li').live("hover", function() {															  
					if(($('.'+$(this).attr('class')+' .chk_span input[type=checkbox]').is(':checked') == false) && ($(this).has("ul").length >= 1)){
						$(this).css('background-color','#FFEEDD');
					}
				});	
				$('#'+self.id+'_left ul li ul li').live("hover", function() {
					if($('.'+$(this).attr('class')+' .chk_chk_span input[type=checkbox]').is(':checked') == false){
						$(this).css('background-color','#FFEEDD');
					}
					$(this).parent().parent().css('background-color','');
				});	
				$('#'+self.id+'_left ul li').live("mouseleave", function() {		
					if(($('.'+$(this).attr('class')+' .chk_span input[type=checkbox]').is(':checked') == false) && ($(this).has("ul").length >= 1)){
						$(this).css('background-color','');
					}
				});	
				$('#'+self.id+'_left ul li ul li').live("mouseleave", function() {
					if($('.'+$(this).attr('class')+' .chk_chk_span input[type=checkbox]').is(':checked') == false){
						$(this).css('background-color','');
					}
					$(this).parent().parent().css('background-color','#FFEEDD');
				});
				$('#'+self.id+'_right ul li').live("hover", function(){															  
						$(this).css('background-color','#FFEEDD');
				});	
				$('#'+self.id+'_right ul li').live("mouseleave", function() {		
					$(this).css('background-color','');
				});				
				/************************** END **************************/				
				
		},
		_create: function(){ 
			this._prepare();
		},
		destroy: function() {
			$.Widget.prototype.destroy.apply(this, arguments); 
		},

  });
}(jQuery));