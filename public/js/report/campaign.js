	$(function() {
			$(".auto_complete").autocomplete({
				minLength: 1,
				source: function( request, response ) {
						alert(this.value); return;
						$.ajax({
							url: '<?php echo site_url("report/autocomplete");?>',
							dataType: 'json',
							type: 'POST',
							data: {
									id: this.id,
									value: this.value
								},
							success:    
								function(data)
								{
									if(data.response =='true')
									{
										add(data.message);
									}
								}
						});
					},
				select: function(event, ui){
					   alert("done");
					}
			})
	});			
