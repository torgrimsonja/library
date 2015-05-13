<?php
/**********************************************************
 *	/inc/template.php
 *	------------------------
 *  begin               : March 1, 2013
 *  created by:		: Jason Torgrimson
 *  copyright		: (c) Twin Falls High School
 **********************************************************/
 

$template = new page_template();

$template->location = ROOT_PATH;
$template->data_validation = $data_validation;
$template->auth = $auth;


class page_template {
	
	var $data_validation;
	var $location;
	var $auth;
	
	function page_header($title, $htmlHead = ''){
		$html['title'] = $this->data_validation->escape_html($title);
	?>
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8" />
            <title><?php echo $html['title']; ?></title>
            
            <link href="<?php echo $this->location; ?>inc/css/customStyles.css" rel="stylesheet">
            <link href="<?php echo $this->location; ?>inc/css/jqueryMobile.css" rel="stylesheet">
			<script src="<?php echo $this->location; ?>inc/js/jquery-1.9.0.min.js"></script>
           <script src="<?php echo $this->location; ?>inc/js/jqueryMobile.js"></script>
            <script type="text/javascript">
				$(document).ready(function(e) {
					$(document).bind("mobileinit", function(){
					  $.extend(  $.mobile , {
						ajaxFormsEnabled : false,
						ajaxEnabled : false
					  });
					});
				});
			</script>
        <?php echo $htmlHead; ?>
        </head>
        <body>
            <div id="page" data-role="page" data-theme="a">
                <div id="header" data-role="header" data-position="fixed">
                        <h2 style="color:#FFF;">Library Check Inh</h2>
                </div>
                <div data-role="content" class="content" id="centerContainer">
    <?php
	}
	
	function page_footer(){
	?>
        		</div>
                <div data-role="footer" id="footer" style="text-align:center;" data-position="fixed">
                   &copy; <?php echo date('Y');?> Twin Falls School District
                   <br />Developed by The Bruin Tech Squad
                </div>                    
                <!-- end #footer -->
        	</div>
        </body>
        </html>

    <?php
	}

	function errorPage($message){
		$this->page_header('An Error Occurred');
		echo $this->data_validation->escape_html($message);
		$this->page_footer();
	}
	
	function admin_page_header($title, $htmlHead = ''){
		
		$html['title'] = $this->data_validation->escape_html($title);
		?>
        <!DOCTYPE html>
        <html>
        <head>
        <meta charset="utf-8" />
            <title><?php echo $html['title']; ?></title>
            <link href="<?php echo $this->location; ?>inc/css/customStyles.css" rel="stylesheet">
            <link href="<?php echo $this->location; ?>inc/css/jqueryMobile.css" rel="stylesheet">
            <script src="<?php echo $this->location; ?>inc/js/jquery-1.9.0.min.js"></script>
            <script src="<?php echo $this->location; ?>inc/js/jqueryMobile.js"></script>
			<script type="text/javascript">
			document.addEventListener('deviceready', onDeviceReady, false);
			
			function onDeviceReady(){
				$(document).bind("mobileinit", function(){
				  $.extend(  $.mobile , {
					ajaxFormsEnabled : false,
					ajaxEnabled : false,
				  });
				});
			}
			</script>
        <?php echo $htmlHead; ?>
        </head>
        <body>
            <div id="page" data-role="page" data-theme="a">
                <div id="header" data-role="header" data-position="fixed">
                        <h2 style="color:#FFF;">Library Check In - Administration</h2>
                        <?php if($this->auth->check_authenticated()){ ?>
                                <a href="?logout" data-ajax="false" data-role="button">Logout</a>
						<?php } ?>
                </div>
					<?php
                        if($this->auth->check_authenticated()){
							?>
                                <div data-role="navbar" data-grid="d">
                                    <ul>															   <!-- class="ui-btn-active" -->
                                        <li><a href="<?php echo $this->location; ?>admin/schedule/index.php">Schedules</a></li>
                                        <li><a href="<?php echo $this->location; ?>admin/options/index.php">Options</a></li>
                                        <li><a href="<?php echo $this->location; ?>admin/report/index.php" data-ajax="false">Reports</a></li>
                                        <li><a href="<?php echo $this->location; ?>admin/user/index.php">Users</a></li>
                                        <li><a href="<?php echo $this->location; ?>admin/settings/index.php">Settings</a></li>
                                    </ul>
                                </div><!-- /navbar -->
                            <?php
						}
					?>
				<div style="margin: 2em;" class="content">
            
    <?php
	}
	
	function admin_page_footer(){
	?>
                    </div>
                    <div data-role="footer" data-position="fixed" class="ui-bar" style="text-align: center;">
                       &copy; <?php echo date('Y');?> Twin Falls School District - TFHS
                    </div>                    
                </div>
            </div>
        </body>
        </html>

    <?php
	}

}