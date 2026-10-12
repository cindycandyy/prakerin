<?php
$auth    = $this->session->userdata("SES_PRIV");
        if(!empty($auth)){
?>
          <!-- Content Row -->
          <div class="row">
            <div class="col-lg-6 mb-4">
        <?php
        }
        ?>
              <!-- Illustrations -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Tutorial<?php
                  if($auth==2){
                      print "&nbsp;<span data-toggle='modal' data-target='#myModalTutor' class='pull-right btn btn-sm btn-success'><em class='fa fa-edit'></em></span>";
                  }
                  ?></h6>
                </div>
                <div class="card-body">
                  <div class="text-center">
                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="<?php echo base_url(); ?>gambar/undraw_posting_photo.svg" alt="">
                  </div>
                 <?php
                 
                 $x=$this->model_tutorial->viewalldata();
                 $dat='';
                 foreach($x as $key => $datax){
                     echo $datax->description;
                    $dat=$datax->description;
                    $id=$datax->id;
                 }
                 ?>
                </div>
                 
              </div> 
                <!-- Divider -->
                <!--<hr class="sidebar-divider">-->
    <?php 
    if($auth==2){
    ?>
    <div class="modal" id="myModalTutor">
				  <div class="modal-dialog modal-lg">
					<div class="modal-content">
					  <div class="modal-header">
						<h4 class="modal-title">TUTORIAL</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					  </div>
					  <div class="modal-body">
					      <!-- Include stylesheet -->
                            <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                            <!-- Create the editor container --><div id="toolbar"></div>
                            <div id="editor">
                              <?php
                              echo $dat;
                              ?>
                            </div>
                            <input type="hidden" name="hiddenInput" id="hiddenInput">
                            <input type='hidden' id="id1" value="<?php echo $id;?>"/>
                            <!-- Include the Quill library -->
                            <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
                            <script src="//unpkg.com/quill-html-edit-button@2.2.7/dist/quill.htmlEditButton.min.js"></script>
                            <!-- Initialize Quill editor -->
                            <script>
                            var toolbarOptions = [['bold', 'italic'], ['link', 'image']];
                            Quill.register("modules/htmlEditButton", htmlEditButton);
                              var quill = new Quill('#editor', {
                                modules: {
                                        'toolbar': [
                                          [{ 'font': [] }, { 'size': [] }],
                                          [ 'bold', 'italic', 'underline', 'strike' ],
                                          [{ 'color': [] }, { 'background': [] }],
                                          [{ 'script': 'super' }, { 'script': 'sub' }],
                                          [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block' ],
                                          [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
                                          [ 'direction', { 'align': [] }],
                                          [ 'clean' ]
                                    ],
                                    htmlEditButton: {
                                        debug: true, // logging, default:false
                                        msg: "Edit the content in HTML format", //Custom message to display in the editor, default: Edit HTML here, when you click "OK" the quill editor's contents will be replaced
                                        okText: "Ok", // Text to display in the OK button, default: Ok,
                                        cancelText: "Cancel", // Text to display in the cancel button, default: Cancel
                                        buttonHTML: "&lt;&gt;", // Text to display in the toolbar button, default: <>
                                        buttonTitle: "Show HTML source", // Text to display as the tooltip for the toolbar button, default: Show HTML source
                                        syntax: false, // Show the HTML with syntax highlighting. Requires highlightjs on window.hljs (similar to Quill itself), default: false
                                        prependSelector: 'div#myelement', // a string used to select where you want to insert the overlayContainer, default: null (appends to body),
                                        editorModules: {} // The default mod
                                      }
                                  },
                              theme: 'snow',
                              });
                            </script>
					  </div>
					  <div class="modal-footer"><span class="btn btn-success btn-sm" onClick="editTutor('id1','editor')">Ubah</span></div>
					</div>
				  </div>
				</div>
				<div class="modal" id="myModalSyarat">
				  <div class="modal-dialog">
					<div class="modal-content">
					  <div class="modal-header">
						<h4 class="modal-title">SYARAT UMUM
						</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					  </div>
					  <div class="modal-body">
					      <!-- Include stylesheet -->
                            <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                           <div id="toolbar"></div>
                            <div id="editor2">
                                <?php    
                                 $z=$this->model_tutorial->viewalldata2();
                                 $dats='';
                                 foreach($z as $key => $datay){
                                echo $datay->description;
                                    $dats=$datay->description;
                                    $id2=$datay->id;
                                 }?>
                            </div>
                            <input type="hidden" name="hiddenInput2" id="hiddenInput2">
                            <input type='hidden' id="id2" value="<?php echo $id2;?>"/>
                            <!-- Include the Quill library -->
                            <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
                            <!-- Initialize Quill editor -->
                            <script>
                            var toolbarOptions = [['bold', 'italic'], ['link', 'image']];
                              var quill2 = new Quill('#editor2', {
                                modules: {
                                        'toolbar': [
                                          [{ 'font': [] }, { 'size': [] }],
                                          [ 'bold', 'italic', 'underline', 'strike' ],
                                          [{ 'color': [] }, { 'background': [] }],
                                          [{ 'script': 'super' }, { 'script': 'sub' }],
                                          [{ 'header': '1' }, { 'header': '2' }, 'blockquote', 'code-block' ],
                                          [{ 'list': 'ordered' }, { 'list': 'bullet'}, { 'indent': '-1' }, { 'indent': '+1' }],
                                          [ 'direction', { 'align': [] }],
                                          [ 'clean' ]
                                    ]
                                  },
                              theme: 'snow',
                              });
                            </script>
					  </div>
					   <div class="modal-footer"><span class="btn btn-success btn-sm" onClick="editSyarat('id2','editor2')">Ubah</span></div>
					</div>
				  </div>
				</div>
            <?php
    }
            if(!empty($auth)){
                ?>
            </div> 
            <div class="col-lg-6 mb-4">
                <!-- Illustrations -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Persyaratan Umum<?php
                  if($auth==2){
                      print "&nbsp;<span data-toggle='modal' data-target='#myModalSyarat' class='pull-right btn btn-sm btn-success'><em class='fa fa-edit'></em></span>";
                  }
                  ?></h6>
                </div>
                <div class="card-body">
                    <p>
                    <?php    
                 $z=$this->model_tutorial->viewalldata2();
                 $dats='';
                 foreach($z as $key => $datay){
                     echo $datay->description;
                    $dats=$datay->description;
                 }?>
                    </p>
                </div>
              </div>
              <!-- Illustrations -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Persyaratan Khusus</h6>
                </div>
                <div class="card-body">
                    <p>
                        <?php $this->view('persyaratan/list_persyaratan')?>
                    </p>
                </div>
              </div>
            </div>
          </div>
          
        <?php
        }
        if($auth==2){
            ?>
            <script>
    function editTutor(a,b) {
        var des     = quill.root.innerHTML;
        var id     = $('#'+a).val();
        if (des != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>tutorial/data/edit",
                data: {
                    desk: des,
                    id: id,
                },
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan.');
                    window.location.href = "<?php echo base_url(); ?>tutorial/data/list";
                },
                error: function (data) {
                    alert('Salah');
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function editSyarat(a,b) {
        var des     = quill2.root.innerHTML;
        var id     = $('#'+a).val();
        if (des != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>tutorial/data/edit",
                data: {
                    desk: des,
                    id: id,
                },
                cache:false,
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan.');
                    window.location.href = "<?php echo base_url(); ?>tutorial/data/list";
                },
                error: function (data) {
                    alert('Salah');
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
                </script>
            <?php
        }
        ?>