<div id="footer">

    	<div class="inner">

			<p><?php _e('Copyright &copy;',APP_TD); ?> <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?></p>

		</div><!-- end inner -->

</div><!-- end footer -->

<?php wp_enqueue_script("jquery"); ?>

<script type="text/javascript">
	var $j = jQuery.noConflict();	
  
  $j(function(){
  
  $j(".add-spreadsheet-column").click(function(){
    
    	$j('.spreadsheet').find('tr').each(function(){
        $j(this).find('td').eq(0).after('<td class="text" contenteditable="true">Test Content</td>');
    });
    
    });//click function
    
    $j(".add-spreadsheet-row").click(function(){
    	
      var count = $j("tr:last td").length;
      var tdString = [];
      var column;
      
      for (i = 0; i < count; i++) {
        column += "<td class='text' contenteditable='true'>Test Content</td>";
      }
      
      
      $j('.spreadsheet > tbody:first').append('<tr>'+column+'</tr>');	
      
    });
    
    $j('.alert-table-data').click(function(){
    		
      	  	var count_row = $j("tr").length;
        var count_column = $j("tr:last td").length;
        
        var spreadsheet_data = [];
      	var spreadsheet_columns = [];	
      	var spreadsheet_cell;  
      
        for(tableRow = 0; tableRow < count_row; tableRow++) {
        		for(tableColumn = 0; tableColumn < count_column; tableColumn++) {
              
              spreadsheet_cell = $j('.spreadsheet tr:eq('+tableRow+')').find('td:eq('+tableColumn+')').text();
              
              spreadsheet_columns.push(spreadsheet_cell);
              
            }
          spreadsheet_data.push(spreadsheet_columns);
        }
      
        $j("#tableData").val("");
        $j("#tableData").val(JSON.stringify(spreadsheet_data));
    });
    
    
    $j(".submit").live("click", function() {
    	
      var count_row = $j("tr").length;
        var count_column = $j("tr:last td").length;
        
        var spreadsheet_data = [];
      	var spreadsheet_columns = [];	
      	var spreadsheet_cell;  
      
        for(tableRow = 0; tableRow < count_row; tableRow++) {
        		for(tableColumn = 0; tableColumn < count_column; tableColumn++) {
              
              spreadsheet_cell = $j('.spreadsheet tr:eq('+tableRow+')').find('td:eq('+tableColumn+')').text();
              
              spreadsheet_columns.push(spreadsheet_cell);
              
            }
          spreadsheet_data.push(spreadsheet_columns);
        }
      
        $j("#tableData").val(JSON.stringify(spreadsheet_data));
    
    });
    

		//for save evaluation form    
    $j("#save_score").click(function(){
      
      var formData = $j(".final-evaluation-form").serialize();
      
      var ajaxurl = "/wp-admin/admin-ajax.php";
        
      $j.ajax({
        url: "/wp-admin/admin-ajax.php",
        type:"POST",
        data: formData + "&action=save_evaluation",
        beforeSend: function() {
            
         }, 
        success: function(){
          	window.location.reload();
  		  },
        error: function(xhr, status, error) {
  			  alert(xhr.responseText);
 				}
      });//ajax
      
    });
    
    $j(".jobs_dropdown").change(function(){
    	
      var resume_count = $j("#employer_evaluation .resumes li").length;
      
      for (i = 0;i < resume_count;i++) {
      	
        var job_title = $j("#employer_evaluation .resumes li:eq("+i+") .job_applying_for_link").text();
        var dropdown_title = $j(".jobs_dropdown option:selected").text();
        
        if (dropdown_title == "All Recent Evaluations") {
          $j("#employer_evaluation .resumes li:eq("+i+")").show();
        } else {
        	if (job_title != dropdown_title) {
            $j("#employer_evaluation .resumes li:eq("+i+")").hide();
         } else {
         		$j("#employer_evaluation .resumes li:eq("+i+")").show();
         } 
        
        }
        
      }
      
    });
    
    
  }); //no conflict function 		
      
</script>
      

