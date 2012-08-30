	$(function() {
			$(".auto_complete").autocomplete({
				minLength: 1,
				source: function( request, response ) {				
						var classNames = $(".auto_complete").prop("class");
						var classNamesArr = classNames.split(' ');						
						$.ajax({
							url: <?php echo site_url('report/autocomplete');?>,
							dataType: 'json',
							type: 'POST',
							data: {
									id: classNamesArr[0],
									value: request.term 
								},
							success: function(data){
									if(data.response =='true')
									{
										add(data.message);
									}
								}
						});
					},
				select: function(event, ui){
					   $(id).val(ui.item.key);
					}
			})
	});			
	