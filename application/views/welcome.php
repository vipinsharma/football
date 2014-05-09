<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Core CSS - Include with every page -->
    <link href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- SB Admin CSS - Include with every page -->
    <link href="<?php echo base_url(); ?>assets/css/football-web.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/cmxform.css" rel="stylesheet">

    

</head>

<body class="loginwrap" >

    <div class="container loginwrap1 ">
    	<div class="row ">
         <div class="col-lg-12 col-md-12 bx-size logologin">
         <img  class="img-responsive" src="<?php echo base_url(); ?>assets/images/logo1.png" width="537" height="115">
         </div>
        </div>

        <div id="server_error" style="display:none">
          <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p class="server_error"></p>
          </div>
        </div>
        
        <div id="server_success" style="display:none">
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Thanks !</strong> You Are Now Logged In 
          </div>
        </div>

        <div id="signup_success" style="display:none">
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Thanks ! An activation link has been sent to your mail , please activate your account !</strong> 
          </div>
        </div>
    	<div class="row ">
        <div class="col-lg-10 col-md-10 bx-size loginbox">
        	<div class=" col-lg-4 col-md-4 ticketholderlogin">
                    <div class="loginbxhead">
                        TICKET HOLDER SIGN IN
                    </div>
                     <div class="panel-body">
                        <form action="welcome/validate" method="post" id="login_form" class="membership_form"> 
                            <fieldset>
                              <div id="loader" style="display:none" >
                                <img src="<?php echo base_url(); ?>assets/images/loading.gif" alt="loading..." />
                              </div>
                                <div class="form-group">
                                  <label class="loginlabel">Row and Section</label>
                                    <input class="form-control" placeholder="Email address" name="email" id="email" type="email" autofocus>
                                </div>
                                <div class="form-group">
                                  <label class="loginlabel">Seat Number</label>
                                    <input class="form-control" placeholder="Password" name="password" id="password" type="password" value="">
                                </div>
                                
                                <!-- Change this to a button or input when using this as a form -->
                               <div class="btnwrap"> 
                                  <input type="submit" value="Enter Stadium" class="btnlogin">
                               </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember my ticket
                                    </label><br>
                                   <a class="forgotpassword" href="<?php echo site_url() ?>welcome/forget_password">Forgot my seat number</a>
                                </div>
                                
                            </fieldset>
                        </form>
                    </div>
                </div> 
          <div class=" col-lg-8 col-md-8 ticketholderlogin">
                    <div class="loginbxhead">
                       OPENING DAY! ... AND YOU DON’T HAVE A TICKET?
            </div>
                    <div class="headrimg">
                    	<img  class="img-responsive" src="<?php echo base_url(); ?>assets/images/get_ticket.png" >
                  </div>
            <div class="panel-body">
              <form action="welcome/add_member" method="post" id="signup_form" class="membership_form"> 
                            <fieldset>
                            	<div class=" col-lg-7 col-md-7 leftsec">
                            	    <div class="form-group">                                
                                    <input class="form-control" placeholder="Email address" name="email" id="email2" type="email" autofocus>
                                </div>
                              		<div class="form-group txtfieldps">    
                                     <input class="form-control" placeholder="Password" name="password" id="password2" type="password" value="">
                                     <input class="form-control confirmpassword" placeholder="Confirm Password" name="password1" id="password1" type="password" value="">
                               	 </div>
                                </div>
                                
                                <!-- Change this to a button or input when using this as a form -->
                                <div class="col-lg-5 col-md-5 rightsec ">
                           	    <div class="btnwrap">
                                  <input type="submit" value="GET YOUR TICKET" class="btnlogin">
                               </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">I have read the 
                                    </label>
                                   <a class="forgotpassword" href="#">Terms</a>.
                                </div>
                                </div>
                            </fieldset>
              </form>
                    </div>
                </div>
             <div class="clearfix"></div>         
        </div>
 
      </div>
    
        
    </div>

    <!-- Core Scripts - Include with every page -->
    <script src="<?php echo base_url(); ?>assets/js/jquery-1.10.2.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery.metisMenu.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    
    <script src="<?php echo base_url(); ?>assets/libs/jquery.mockjax.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/jquery.form.js"></script>
    <script src="<?php echo base_url(); ?>assets/libs/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/libs/common-js.js"></script>


<script type="text/javascript">
//login form validation 
jQuery(function() {
    // show a simple loading indicator
    var loader = jQuery('<div id="loader"><img src="<?php echo base_url(); ?>assets/images/loading.gif" alt="loading..." /></div>')
      .css({position: "relative", top: "1em", left: "25em", display: "inline"})
      .appendTo("body")
      .hide();
    jQuery().ajaxStart(function() {
      loader.show();
    }).ajaxStop(function() {
      loader.hide();
    }).ajaxError(function(a, b, e) {
      throw e;
    });

    var loginUrl = '<?php echo base_url(); ?>welcome/ajax_validate';
    var v = jQuery("#login_form").validate({
      rules: {
      email: {
        required: true,
        minlength: 3
      },
      password: {
        required: true,
        minlength: 6
      }
    },
    messages: {

      username: {
        required: "Please enter a username",
        minlength: "Your username must consist of at least 3 characters"
      },
      password: {
        required: "Please provide a password",
        minlength: "Your password must be at least 6 characters long"
      }
    },
      submitHandler: function(form) {
        $("#server_error").css("display","none");
        $("#server_success").css("display","none");
        $(".server_error").html('');
        jQuery(form).ajaxSubmit({
          url: loginUrl, type: 'post',
          success : function (response) {
            if(response==1){
                $("#server_error").css("display","none");
                $("#server_success").css("display","block");
                $(".alert-success").fadeOut(1000, function () {
                setTimeout(function () { window.location.href = "<?php echo base_url(); ?>private/home"; }, 1000);
                });
            }else{
              $("#server_success").css("display","none");
              $("#server_error").css("display","block");
              $(".server_error").html(response);
            }
          }
        });
      }
    });
});
//end of login form validation 

//login signup validation 
jQuery(function() {
    // show a simple loading indicator
    var loader = jQuery('<div id="loader"><img src="<?php echo base_url(); ?>assets/images/loading.gif" alt="loading..." /></div>')
      .css({position: "relative", top: "1em", left: "25em", display: "inline"})
      .appendTo("body")
      .hide();
    jQuery().ajaxStart(function() {
      loader.show();
    }).ajaxStop(function() {
      loader.hide();
    }).ajaxError(function(a, b, e) {
      throw e;
    });

jQuery.validator.addMethod("checkAvailability",function(value,element){
  var checkUrl = '<?php echo base_url(); ?>welcome/check_mail';

   var x= $.ajax({
      url: checkUrl,
      type: 'POST',
      async: false,
      data: "email=" + value,
      success: function(xml) {
        if(xml){
            return true;
        }else{
          return false;
        }
      }
   })
},"Sorry, this user email is already taken !");


    var signupUrl = '<?php echo base_url(); ?>welcome/ajax_add_member';
    var v = jQuery("#signup_form").validate({
      rules: {
      email: {
        required: true,
        email:true/*,
        checkAvailability: true*/
      },
      password: {
        required: true,
        minlength: 6
      },
      password1: {
        required: true,
        minlength: 6,
        equalTo: "#password2"
      }
    },
      submitHandler: function(form) {
        $("#server_error").css("display","none");
        $("#signup_success").css("display","none");
        $(".server_error").html('');
        jQuery(form).ajaxSubmit({
          url: signupUrl, type: 'post',
          success : function (response) {
            if(response==1){
                $("#server_error").css("display","none");
                $("#signup_success").css("display","block");
                $(".alert-success").css("display","block");
            }else{
              $("#signup_success").css("display","none");
              $("#server_error").css("display","block");
              $(".server_error").html(response);
            }
          }
        });
      }
    });
});
//end of signup form validation 


</script>
</body>

</html>
