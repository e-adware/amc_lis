<!--header-->
<div id="content-header">
  <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
  <table class="table table-bordered table-condensed">
    <tr>
      <th>Select Test<br />
        <select class="span6" id="testid" onchange="load_test_info(this.value)">
          <option value="0">--Select--</option>
          <?php
						$test=mysqli_query($link,"select * from testmaster where category_id>1 order by testname");
						while($t=mysqli_fetch_array($test))
						{
							echo "<option value='$t[testid]'>$t[testname]</option>";
						}
					?>
        </select>
      </th>
      <th>Select Doctor <br />
        <select id="doctor" onchange="load_test_info($('#testid').val())">
          <option value="0">All</option>
          <?php
						$qry=mysqli_query($GLOBALS["___mysqli_ston"], "select * from lab_doctor where category='2'");	
						while($q=mysqli_fetch_array($qry))
						{
							echo "<option value='$q[id]'>$q[name]</option>";	
						}
					?>
        </select>
      </th>
    </tr>
    <tr>

      <td colspan="2">
        <!-- <textarea style="height:350px;width:1000px" name="article-body" id="txtdetail"></textarea> -->
        
        <div class="document-editor">
          <div class="document-editor__toolbar"></div>
          <div class="document-editor__editable-container editor-container">
            <div class="document-editor__editable editor" id="text_body">

            </div>
          </div>
        </div>


      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center">
        <input type="button" id="save" value="Save" class="btn btn-custom" onclick="save_normal()" />
      </td>
    </tr>
  </table>
</div>
<style>
.document-editor {
  border: 1px solid var(--ck-color-base-border);
  border-radius: var(--ck-border-radius);

  /* Set vertical boundaries for the document editor. */
  max-height: 700px;

  /* This element is a flex container for easier rendering. */
  display: flex;
  flex-flow: column nowrap;
}

/* Set the default font for the "page" of the content. */
.document-editor .ck-content,
.document-editor .ck-heading-dropdown .ck-list .ck-button__label {
  font: 16px/1.6 "Helvetica Neue", Helvetica, Arial, sans-serif;
}

/* Make the editable container look like the inside of a native word processor application. */
.document-editor__editable-container {
  padding: calc(2 * var(--ck-spacing-large));
  /* background: var(--ck-color-base-foreground); */

  /* Make it possible to scroll the "page" of the edited content. */
  overflow-y: scroll;
  /* align-self: center; */
}

.document-editor__editable-container .ck-editor__editable {
  /* Set the dimensions of the "page". */
  width: 15.8cm;
  min-height: 21cm;

  /* Keep the "page" off the boundaries of the container. */
  padding: 1cm 2cm 2cm;

  border: 1px hsl(0, 0%, 82.7%) solid;
  border-radius: var(--ck-border-radius);
  background: white;

  /* The "page" should cast a slight shadow (3D illusion). */
  box-shadow: 0 0 5px hsla(0, 0%, 0%, .1);

  /* Center the "page". */
  margin: 0 auto;
}

/* Set the default font for the "page" of the content. */
.document-editor .ck-content,
.document-editor .ck-heading-dropdown .ck-list .ck-button__label {
  font: 16px/1.6 "Helvetica Neue", Helvetica, Arial, sans-serif;
}

/* Adjust the headings dropdown to host some larger heading styles. */
.document-editor .ck-heading-dropdown .ck-list .ck-button__label {
  line-height: calc(1.7 * var(--ck-line-height-base) * var(--ck-font-size-base));
  min-width: 6em;
}

/* Scale down all heading previews because they are way too big to be presented in the UI.
Preserve the relative scale, though. */
.document-editor .ck-heading-dropdown .ck-list .ck-button:not(.ck-heading_paragraph) .ck-button__label {
  transform: scale(0.8);
  transform-origin: left;
}

/* Set the styles for "Heading 1". */
.document-editor .ck-content h2,
.document-editor .ck-heading-dropdown .ck-heading_heading1 .ck-button__label {
  font-size: 2.18em;
  font-weight: normal;
}

.document-editor .ck-content h2 {
  line-height: 1.37em;
  padding-top: .342em;
  margin-bottom: .142em;
}

/* Set the styles for "Heading 2". */
.document-editor .ck-content h3,
.document-editor .ck-heading-dropdown .ck-heading_heading2 .ck-button__label {
  font-size: 1.75em;
  font-weight: normal;
  color: hsl(203, 100%, 50%);
}

.document-editor .ck-heading-dropdown .ck-heading_heading2.ck-on .ck-button__label {
  color: var(--ck-color-list-button-on-text);
}

/* Set the styles for "Heading 2". */
.document-editor .ck-content h3 {
  line-height: 1.86em;
  padding-top: .171em;
  margin-bottom: .357em;
}

/* Set the styles for "Heading 3". */
.document-editor .ck-content h4,
.document-editor .ck-heading-dropdown .ck-heading_heading3 .ck-button__label {
  font-size: 1.31em;
  font-weight: bold;
}

.document-editor .ck-content h4 {
  line-height: 1.24em;
  padding-top: .286em;
  margin-bottom: .952em;
}

/* Set the styles for "Paragraph". */
.document-editor .ck-content p {
  font-size: 1em;
  line-height: 1.34em;
  padding-top: .5em;
  margin-bottom: 1.5em;
  color: #000;
}
</style>
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.2.0/decoupled-document/ckeditor.js"></script> -->
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.2.0/classic/ckeditor.js"></script> -->
<script type="text/javascript" src="../ckeditor5/build/ckeditor.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>

$(document).ready(function()
{
  $("select").select2({
    theme: "classic"
  });
  $("#exec select:first").focus();
  add();
})

function load_rad_test(val) {
  //alert(val);
  $.post("pages/radiology_normal_test.php", {
      val: val
    },
    function(data, status) {
      $("#rad_test").html(data);
      $("#rad_test").slideDown(500);
    })
}



function load_test_info(id) {
  $.post("pages/radiology_normal_info.php", {
      id: id,
      doctor: $("#doctor").val()
    },
    function(data, status) {
      return window.editor.setData(data);
    })
}

function add() {


  DecoupledDocumentEditor
    .create(document.querySelector('.editor'), {

      licenseKey: '',

      // updateSourceElementOnDestroy: true,

    })
    .then(editor => {
      window.editor = editor;

      document.querySelector('.document-editor__toolbar').appendChild(editor.ui.view.toolbar.element);
      document.querySelector('.ck-toolbar').classList.add('ck-reset_all');
    })
    .catch(error => {
      console.error('Oops, something went wrong!');
      console.error(
        'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:'
      );
      console.warn('Build id: ebw2umoinbfe-jy7c9da3imnc');
      console.error(error);
    });
  // if (CKEDITOR.instances['article-body'])
  // {
  // 	CKEDITOR.instances['article-body'].destroy(true);
  // }
  // CKEDITOR.replace('article-body');
  // CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
  // CKEDITOR.config.extraPlugins = 'lineheight';
  // //CKEDITOR.config.width = 700;		
  // CKEDITOR.config.height = 300;		
  // CKEDITOR.config.line_height="1.0em;1.5em;2.0em;2.5em;3.0em;3.5em;4.0em;4.5em;5.0em;" ;
}


function getEditor() {
  return window.editor.getData();
}

function save_normal() {
  // alert( getEditor() );
  $.post("pages/radiology_normal_save.php", {
      testid: $("#testid").val(),
      doctor: $("#doctor").val(),
      // normal: $(".rad_res").contents().find('body').html()
      normal: getEditor(),
    },
    function(data, status) {
      //alert(data);
      alert("Saved");
    })
}
</script>
<style>
.cke_textarea_inline {
  padding: 10px;
  height: 380px;
  overflow: auto;
  border: 1px solid gray;
  -webkit-appearance: textfield;
}
</style>