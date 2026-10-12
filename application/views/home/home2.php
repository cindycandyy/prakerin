<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <title>E-PRAKERIN| Petrosida Gresik</title>
    <meta content="E-PRAKERIN| Petrosida Gresik" name="description" />
    <meta content="E-PRAKERIN| Petrosida Gresik" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<link rel="shortcut icon" href="assetsfe/img/favicon.png"/>
    <link rel="stylesheet" href="assetsfe/css/bootstrap.min.css">
    <link rel="stylesheet" href="assetsfe/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assetsfe/css/magnific-popup.css">
    <link rel="stylesheet" href="assetsfe/css/font-awesome.min.css">
    <link rel="stylesheet" href="assetsfe/css/themify-icons.css">
    <link rel="stylesheet" href="assetsfe/css/nice-select.css">
    <link rel="stylesheet" href="assetsfe/css/flaticon.css">
    <link rel="stylesheet" href="assetsfe/css/gijgo.css">
    <link rel="stylesheet" href="assetsfe/css/animate.css">
    <link rel="stylesheet" href="assetsfe/css/slicknav.css">
    <link rel="stylesheet" href="assetsfe/css/style.css">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        .nav-link {
            color: #555 !important;
        }
		.whatsapp {
		 width:5%;
		 left: 15px; /* From JQuery we know scroll bar width is 17px but still reducing it 2px*/
		 bottom: 30px;
		 position: fixed;
		}
    </style>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-6GR6909W07"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-6GR6909W07');
</script>
</head>
<body>
       <!-- header-start -->
    <header>
        <div class="header-area ">
            <div class="header-top_area">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-8 col-md-12 col-lg-8">
                            <div class="short_contact_list">
                                <ul>
                                    <li><a href="#"><i class="fa fa-phone"></i>(031) 398 1553 ext. 136</a></li>
                                    <li><a href="#"><i class="fa fa-clock-o"></i>Senin s.d Jumat Pukul 08:00-16:00 WIB</a></li>
                                    <li><a href="#"><i class="fa fa-envelope"></i>prakerin@petrosida-gresik.com</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6 col-lg-4">
                            <div class="social_media_links d-none d-lg-block">
                                <a href="#">
                                    <img src="assetsfe/img/samping.png" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sticky-header" class="main-header-area">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-3">
                            <div class="logo">
                                <a href="index-2.html">
                                    <img src="assetsfe/img/footer_logo.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-9 col-lg-9">
                            <div class="main-menu">
                                <nav>
                                    <ul id="navigation">
                                        <li><a href="#">BERANDA</a></li>
                                        <li class='nav-item dropdown'><a class='nav-link dropdown-toggle' href="#" role="button" data-toggle="dropdown" aria-expanded="false">PROFILE</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#profile">PROFIL PERUSAHAAN</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);" onClick="location.href='<?php echo base_url(); ?>gambar/struktur_organisasi.png';">STRUKTUR ORGANISASI</a></li>
                                          </ul>
                                        </li>
                                       <li><a href="#testimoni">TESTIMONI</a></li>
                                        <li><a href="#kontak">KONTAK</a></li>
										<?php
										$auth = $this->session->userdata("SES_PRIV");
										if ($auth) {
										?>
										<li class='btn btn-warning'><a href="tutorial/data/list">AKUN SAYA</li>
										<?php
										}else{
										?>
										<li class='btn btn-warning'><a href="login/sistem/login_sistem">LOGIN</a></li>
										
										
										<?php
										}
										?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header-end -->

    <!-- slider_area_start -->
    <div class="slider_area">
        <div class="single_slider  d-flex align-items-center slider_bg_1 overlay2">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="slider_text ">
                             <h3><span>BERKEMBANG BERSAMA</span></h3>
                            <h3><span>UNTUK KEMAJUAN PERTANIAN</br>INDONESIA</span></h3>
                            <br>
                            <br>
                            <p style="font-size: 150%;">
                                <strong>PT Petrosida Gresik </strong>memberikan kesempatan bagi pelajar Perguruan Tinggi
                                maupun Sekolah Menengah Atas/Kejuruan di seluruh Indonesia untuk berinovasi bersama kami.
                            </p>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- our_facilitics_area_start  -->
    <div class="our_facilitics_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section_title text-center mb-55">
                        <div class="section-heading text-center mb-100 wow fadeInUp" data-wow-delay="100ms">
                            <div class="col-md-6 text-center heading-section ftco-animate">
                                <h1 class="big">program.</h1>
                            </div>
                            <div class="line"></div>
                            <h3><span>JENIS PROGRAM PRAKTEK KERJA INDUSTRI<img src="assetsfe/img/icon/kanan.png" width='10%'/></span></h3><p>&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="single_feature text-center">
                        <div class="icon">
                            <img style="position:float;margin-top:-85px;" src="assetsfe/img/icon/sma.png" alt="">
                        </br></br></div>
                        <h3>Kerja Praktek Pelajar
                            <br>
                           Kejuruan</h3>
                        <p style="font-size: 95%;">
                            Kerja praktek individu maupun kelompok
                            dengan sasaran pelajar Sekolah Menengah
                            Kejuruan seluruh Indonesia.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single_feature text-center">
                        <div class="icon">
                            <img style="position:float;margin-top:-85px;" src="assetsfe/img/icon/pt.png" alt="">
                        </br></br></div>
                        <h3>Kerja Praktek Pelajar
                            <br>
                            Perguruan Tinggi</h3>
                        <p style="font-size: 95%;">
                            Kerja praktek individu maupun kelompok
                            dengan sasaran pelajar/mahasiswa
                            Perguruan Tinggi seluruh Indonesia.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="single_feature text-center">
                        <div class="icon">
                            <img style="position:float;margin-top:-85px;" src="assetsfe/img/icon/ta.png" alt="">
                        </br></br></div>
                        <h3>Penelitian Pelajar
                            <br>
                            Perguruan Tinggi</h3>
                        <p style="font-size: 95%;">
                            Penelitian individu ataupun kelompok
                            bagi mahasiswa yang melakukan
                            penelitian tugas akhir maupun proyek penelitian.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- our_facilitics_area_end  -->

    <!-- ##### Services Area Start ###### -->

    <br>
    <br>
    <br>
    <br>
      <div class="services-area section-padding-100-0 xx">
        <div class="container services-area">
		<div class="row">
                <div class="col-lg-12">
                    <div class="col-md-6 text-right heading-section ftco-animate">
                        <h1 class="big">benefit.</h1>
                    </div>
                    <!-- Section Heading -->
                    <div class="section_title text-center mb-55">
                        <div class="section-heading text-center mb-100 wow fadeInUp" data-wow-delay="100ms">
                            <div class="line"></div>
                            <h3><span>BENEFIT YANG DIDAPAT<img src="assetsfe/img/icon/kanan.png" width='15%'/></span></h3>
                        </div>
                    </div>
                </div>
            </div>
			<div class="row">				
				 <div class="row" style="margin-bottom:20px;width:100%!important;">	
				 <div class="col-lg-12 col-md-12">
                    <div class="single_featurex">
						<div class='row'>
						<div class='col-lg-2 col-md-2 text-center'><div class="icon"><img style="max-width:auto;width:auto;" src="assetsfe/img/icon/1.png" alt=""></div></div>
							<div class='col-lg-10 col-md-10'>
							
								<div class="text"><p style="font-size: 110%;">
									<strong>Pembimbing yang Berkompeten</strong></br>
									Kami berkomitmen untuk memberikan Pembimbing Kerja Praktek yang berkompeten di bidangnya.
									</p>
								</div>
							
							</div>
						</div>
                    </div>
                </div>
				</div>
				
				 <div class="row" style="width:100%!important;margin-bottom:20px;">	
				 <div class="col-lg-12 col-md-12">
                    <div class="single_featurex">
						<div class='row'>
						<div class='col-lg-2 col-md-2 text-center'><div class="icon"><img src="assetsfe/img/icon/2.png" style="max-width:auto;width:auto;" alt=""></div></div>
							<div class='col-lg-10 col-md-10'>
							<div class="text"><p style="font-size: 110%;">
									<strong>Kesempatan Mempelajari Alat Perusahaan</strong></br>
                            Memberikan kesempatan bagi siswa/mahasiswa untuk mengoperasikan alat di perusahaan.
                        	</p>
								</div>
							
							</div>
						</div>
                    </div>
                </div>
				</div>
				
				 <div class="row" style="margin-bottom:50px;width:100%!important;">	
				 <div class="col-lg-12 col-md-12">
                    <div class="single_featurex">
						<div class='row'>
						<div class='col-lg-2 col-md-2 text-center'><div class="icon"><img src="assetsfe/img/icon/3.png" style="max-width:auto;width:auto;" alt=""></div></div>
							<div class='col-lg-10 col-md-10'>
								<div class="text"><p style="font-size: 110%;">
									<strong>Pengalaman Belajar di Anak Perusahan BUMN</strong></br>
									Kesempatan bagi siswa/mahasiswa untuk merasakan berkontribusi di anak perusahaan BUMN.
									</p>
								</div>
							</div>
						</div>
                    </div>
                </div>
				</div>
			</div>
        </div>
    </div>
    <!-- latest_activites_area_start  -->
    <div class="latest_activites_area">
        <div class=" video_bg_1 video_activite  d-flex align-items-center justify-content-center">
        </div>
        <div class="container">
            <div class="row justify-content-end">
                <div class="col-lg-6">
                    <div class="activites_info">
                        <div class="col-lg-4 col-md-4 text-center heading-section ftco-animate">
                            <h1 class="big">tentang.</h1>
                        </div>
                        <div class="single-features-area mb-100 wow fadeInUp" data-wow-delay="100ms">
                            <!-- Section Heading -->
                            <div class="section-heading">
                                <div class="line"></div>
                                <div class="section_title">
                                    <h3><span id="profile">Tentang Petrosida Gresik</span></h3>
                                </div>
                            </div>
                        </div>
                        <p class="para_1">Kami adalah perusahaan Agroindustri yang profesional, berpotensi dan berkembang di Indonesia. Kami adalah anak perusahaan dari PT Petrokimia Gresik perusahaan pupuk terbesar dan terlengkap di Indonesia. kami berdiri sejak tahun 1984 dengan memproduksi Bahan Aktif kimia untuk pertanian dan perkebunan pertama kali di Indonesia.</p>
                        <p class="para_2">Dalam jangka waktu 20 tahun terakhir ini, kami menyediakan produk-produk kimia pertanian (agrokimia) seperti Pestisida, Pupuk serta Bahan kimia untuk penunjang Industri Agrokimia. Dengan luas lahan pertanian dan perkebunan di Indonesia yang begitu besar didukung dengan pengalaman kami selama lebih dari 30 tahun di Industri Agrokimia, kami berusaha berperan serta dan memberikan yang terbaik dengan produk-produk yang berkualitas kepada para petani dan pelaku usaha yang bergerak di bidang pertanian dan perkebunan untuk menyelamatkan hasil panennya serta membantu pemerintah menciptakan swasembada pangan di Indonesia</p>
                        <p class="para_3">Saat ini kami sedang mengembangkan produk-produk Bio Pestisida dan Bio Fertilizer untuk mendukung gerakan Go Green di Indonesia. Dengan wilayah pemasaran kami yang mencakup wilayah di seluruh indonesia, kami berusaha untuk lebih dekat dengan pelanggan dan memberikan pelayanan terbaik.</p>
						<br>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="reson_area section_padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="col-md-6 text-center heading-section ftco-animate">
                        <h1 class="big" id="testimoni">testimoni.</h1>
                    </div>
                    <div class="section_title text-center mb-55">
                        <div class="section-heading text-center mb-100 wow fadeInUp" data-wow-delay="100ms">
                            <div class="line"></div>
                            <h3><span>Testimoni<img src="assetsfe/img/icon/kanan.png" width='25%'/></span></h3>
                            <p>Peserta Praktek Kerja Industri</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-5">
                    <div class="single_reson">
                        <div class="thum overlay3">
                            <div class="thum_1" style="height:250px">
                                <img src="assetsfe/img/help/1.jpg" alt="">
                            </div>
                        </div>
                        <div class="help_content">
                            <h4>Fritztha Rafandha</h4>
                            <h5>Universitas Brawijaya Malang</h5>
                            <p style="height:170px;overflow-y:auto;font-size: 65%!important;">"Saya diberikan kesempatan untuk mendapatkan pengalaman baru, menerapkan ilmu yang sebelumnya hanya saya dapatkan secara teori, serta menambah pengetahuan saya dalam dunia kerja. Selain itu, saya juga dikelilingi oleh pegawai yang baik dan membimbing saya"</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-5">
                    <div class="single_reson">
                        <div class="thum overlay3">
                            <div class="thum_1" style="height:250px">
                                <img src="assetsfe/img/help/2.jpg" alt="">
                            </div>
                        </div>
                        <div class="help_content">
                            <h4>Malliano Ahmad Yusuf</h4>
                            <h5>Universitas Nahdlatul Ulama Surabaya</h5>
                            <p style="height:170px;overflow-y:auto;font-size: 65%!important;">"PT Petrosida Gresik membuat saya belajar banyak pengalaman dan pengetahuan baru yang bisa saya dapatkan disini, saya bisa bertemu dengan tim yang sangat berkompeten dan konsisten dalam melaksanakan pekerjaannya"</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-5">
                    <div class="single_reson">
                        <div class="thum overlay3">
                            <div class="thum_1" style="height:250px">
                                <img src="assetsfe/img/help/3.jpg" alt="">
                            </div>
                        </div>
                        <div class="help_content">
                            <h4>Shaffiyah Ais Karima</h4>
                            <h5>Universitas Brawijaya</h5>
                            <p style="height:170px;overflow-y:auto;font-size: 65%!important;">"Magang 6 bulan di PT Petrosida Gresik menjadi pengalaman yang berkesan karena banyak kegiatan yang seru serta pegawai yang ramah dan menyenangkan. Terima kasih PT Petrosida Gresik, semoga menjadi perusahaan agroindustri yang terkemuka di Indonesia"</p>
                        </div>
                    </div>
                </div>
				<div class="col-lg-3 col-md-5">
                    <div class="single_reson">
                        <div class="thum overlay3">
                            <div class="thum_1" style="height:250px">
                                <img src="assetsfe/img/help/4.jpg" alt="">
                            </div>
                        </div>
                        <div class="help_content">
                            <h4>Windi Farra Dila</h4>
                            <h5>SMK Negeri Mojoagung</h5>
                            <p style="height:170px;overflow-y:auto;font-size: 65%!important;">"Melakukan PKL di PT Petrosida Gresik merupakan kesempatan yg tak terduga, memberikan banyak ilmu dan pengalaman untuk bekerja secara kelompok dan konsisten terhadap pekerjaan yang diberikan, sukses selalu untuk PT Petrosida Gresik"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- reson_area_end  -->

    <br>
    <br>
    <!-- counter_area  -->
    <div class="counter_area">
        <div class="container">
            <div class="offcan_bg">
                <div class="row">
                    <div class="col-xl-3 col-md-3">
                        <div class="single_counter text-center">
                            <h3><span class="counter">20</span> <span>+</span> </h3>
                            <p>Kampus Mitra</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <div class="single_counter text-center">
                            <h3><span class="counter"><?php echo ($this->model_depag->getJmlfield(2, "kuota"))?></span> <span>+</span> </h3>
                            <p>Kuota Peserta</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <div class="single_counter text-center">
                            <h3><span class="counter">500</span></h3>
                            <p>Peserta Terdaftar</p>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3">
                        <div class="single_counter text-center">
                            <h3><span class="counter">1000</span></h3>
                            <p>Total Peserta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /counter_area  -->

    <!-- footer_start  -->
    <footer class="footer">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-md-6 col-lg-4 ">
                        <div class="footer_widget">
                            <div class="footer_logo">
                                <a href="#">
                                    <img src="assetsfe/img/logo.png" alt="">
                                </a>
                            </div>
                            <p class="address_text">
                                <i>Electronic</i>-Praktek Kerja Industri<br>
                                adalah Sistem Informasi Pendaftaran dan
                                <br>
                                Pengelolaan Praktek Kerja Industri dan
                                <br>Penelitian PT Petrosida Gresik</br>
                                .
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-lg-2">
                        <div class="footer_widget">
                            <h3 class="footer_title">PENGEMBANGAN ORGANISASI &
                                <br>
                                SUMBER DAYA MANUSIA
                            </h3>
                            <h4><strong>E-MAIL</strong></h4>
                            <p class="address_text">prakerin@petrosida-gresik.com</p>
                            <br>
                            <h4><strong>ALAMAT</strong></h4>
                            <p class="address_text"> Jl. KIG Raya Utara Kav. O No. 5, Kawasan Industri Gresik (KIG), Kec. Gresik, Kabupaten Gresik.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-lg-3">
                        <div class="footer_widget">
                            <h3 class="footer_title"></h3>
                            <br>
                            <br>
                            <div id="kontak" class="contacts">
                                <h4><strong>KONTAK KAMI</strong></h4>
                                <p class="address_text"><em class="fa fa-phone"></em> (031) 398 1553 ext. 136</br><!--<em style="color:green;" class="fa fa-phone-square bg-light"></em> +62 812 3055 4201 (Roys) Chat Only</br>--></p>
                                <br>
                                <h4><strong>PELAYANAN</strong></h4>
                                <p class="address_text">Hari : Senin s.d. Jumat<br />
Pukul : 08.00 - 16.00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copy-right_text">
            <div class="container">
                <div class="row">
                    <div class="bordered_1px "></div>
                    <div class="col-xl-12">
                        <p class="copy_right text-center">
                            <p>
                                Copyright &copy;<script>document.write(new Date().getFullYear());</script>
                                Petrosida Gresik. All Rights Reserved
                                <br>
                             </p>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer_end  -->
    </footer>
    <!-- footer_end  -->

    <!-- link that opens popup -->

    <!-- JS here -->
    <script src="assetsfe/js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="assetsfe/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="assetsfe/js/popper.min.js"></script>
    <script src="assetsfe/js/bootstrap.min.js"></script>
    <script src="assetsfe/js/owl.carousel.min.js"></script>
    <script src="assetsfe/js/isotope.pkgd.min.js"></script>
    <script src="assetsfe/js/ajax-form.js"></script>
    <script src="assetsfe/js/waypoints.min.js"></script>
    <script src="assetsfe/js/jquery.counterup.min.js"></script>
    <script src="assetsfe/js/imagesloaded.pkgd.min.js"></script>
    <script src="assetsfe/js/scrollIt.js"></script>
    <script src="assetsfe/js/jquery.scrollUp.min.js"></script>
    <script src="assetsfe/js/wow.min.js"></script>
    <script src="assetsfe/js/nice-select.min.js"></script>
    <script src="assetsfe/js/jquery.slicknav.min.js"></script>
    <script src="assetsfe/js/jquery.magnific-popup.min.js"></script>
    <script src="assetsfe/js/plugins.js"></script>
    <script src="assetsfe/js/gijgo.min.js"></script>
    <!--contact js-->
    <script src="assetsfe/js/contact.js"></script>
    <script src="assetsfe/js/jquery.ajaxchimp.min.js"></script>
    <script src="assetsfe/js/jquery.form.js"></script>
    <script src="assetsfe/js/jquery.validate.min.js"></script>
    <script src="assetsfe/js/mail-script.js"></script>

    <script src="assetsfe/js/main.js"></script>

    <!-- Modal -->
    <form method="post" action="//petrosida-gresik.com/prakerin" id="ctl00">

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true" data-backdrop="false"  style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle-2">Kuota Unit Kerja</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                
            </div>
        </div>
    </div>
    </form>
	<a href="https://wa.me/6281230554201" target="_blank"><img class="whatsapp" src="assetsfe/img/icon/wa.png"/></a>
</body>
</html>
