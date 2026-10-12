<?php $this->load->view('header'); ?>

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <?php echo $this->session->flashdata('pesan');?>
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-login-image" style=''></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                              <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" method="POST" action="<?php echo base_url(); ?>register/sistem/daftar">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Nama anda">
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" onchange="validateEmail()" id="email" name="email" placeholder="Email Address">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user" onchange="cekPass()" id="re_password" name="re_password" placeholder="Password">
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-block" type="submit">Register Account</button>
                              </form>
                              <hr>
                              <div class="text-center">
                                    <a class="small" href="<?php echo base_url(); ?>login/sistem/login_sistem">Already have an account? Login!</a>
                              </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<script>
    function cekPass() {
        var password1 = $('#password').val();
        var password2 = $('#re_password').val();
        if (password1 != password2) {
            alert("Password tidak sama, silahkan ketik ulang password anda.");
        }
    }
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