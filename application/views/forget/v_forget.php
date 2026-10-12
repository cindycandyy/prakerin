<?php $this->load->view('header'); ?>

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <?php echo $this->session->flashdata('pesan');?>
                  <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                                    <p class="mb-4">We get it, stuff happens. Just enter your email address below and we'll send you a link to reset your password!</p>
                                </div>
                                <form class="user" method="POST" action="<?php echo base_url(); ?>forget/send_email">
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user" onchange="validateEmail()" id="email" name="email" placeholder="Email Address">
                                    </div>
                                    <button class="btn btn-primary btn-block" type="submit">Reset Password</button>
                                  </form>
                                </form>
                                <hr>
                                <div class="text-center">
                                    <a class="small" href="<?php echo base_url(); ?>register/sistem/register_sistem">Create an Account!</a>
                                </div>
                                <div class="text-center">
                                    <a class="small" href="<?php echo base_url(); ?>login/sistem/login_sistem">Already have an account? Login!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    function validateEmail() {
        var x       = $('#email').val();//document.forms["email"]["email"].value;
        var atpos   = x.indexOf("@");
        var dotpos  = x.lastIndexOf(".");
        if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length)
        {
            alert("Mohon isikan email dengan benar !");
        }
    }
</script>

<?php $this->load->view('footer'); ?>