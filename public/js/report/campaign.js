<script>
	$(function() {
			$(".auto_complete").autocomplete({
				minLength: 1,
				source: function( request, response ) {
						$.ajax({
							url: '<?php echo site_url("report/autocomplete");?>',
							dataType: 'json',
							type: 'POST',
							data: {
									id: request.id,
									value: request.value
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
	</script>