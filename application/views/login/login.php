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
                    <h1 class="h4 text-gray-900 mb-4">Selamat Datang</h1>
                  </div>
                  <form class="user" method="POST" action="<?php echo base_url(); ?>login/sistem/login">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="exampleInputEmail" id="userName" name="userName" placeholder="Email yang didaftarkan" required>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="exampleInputPassword" id="password" name="password" placeholder="Password">
                    </div>
                    
                    <a href="<?php echo base_url(); ?>">
                      <button class="btn btn-secondary btn-block" type="button">Batal</button>
                    </a>
                      &nbsp;
                    <button class="btn btn-info btn-block" type="submit">Masuk</button>
                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="<?php echo base_url(); ?>forget">Lupa Password?</a>
                  </div>
                  <div class="text-center">
                    <a class="btn btn-success btn-block" href="<?php echo base_url(); ?>register/sistem/register_sistem">Buat Akun!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>
<?php $this->load->view('footer'); ?>