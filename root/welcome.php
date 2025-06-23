<div id="welcome" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-body">
    <div class="context">
      <!--<h1>Pure Css Animated Background</h1>-->
      <h1>E-Adware wishes</h1>
      <h1><?php echo $gret['messages'];?></h1>
    </div>


    <div class="area">
      <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
      </ul>
    </div>
  </div>
  <div class="modal-footer">
    <!--<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>-->
    <button class="btn btn-danger btnReview" onclick="gretReview('<?php echo base64_encode($gret['msg_id']);?>','1')"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i> Ignore</button>
    <button class="btn btn-primary btnReview" onclick="gretReview('<?php echo base64_encode($gret['msg_id']);?>','2')"><i class="icon-star text-warning"></i><i class="icon-star"></i><i class="icon-star"></i> It's OK</button>
    <button class="btn btn-success btnReview" onclick="gretReview('<?php echo base64_encode($gret['msg_id']);?>','3')"><i class="icon-star text-warning"></i><i class="icon-star text-warning"></i><i class="icon-star text-warning"></i> Thank You</button>
  </div>
</div>

<style>
.modal-body {
  padding: 0px !important;
  max-height: 90% !important;
  overflow: hidden !important;
}

.context {
  width: 100%;
  position: absolute;
  top: 10vh;

}

.context h1 {
  text-align: center;
  color: #fff;
  font-size: 50px;
  padding: 5px;
  line-height: 60px;
}


.area {
  background: #4e54c8;
  //background: #de8b3f;
  background: -webkit-linear-gradient(to left, #8f94fb, #4e54c8);
  width: 100%;
  height: 100vh;


}

.circles {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.circles li {
  position: absolute;
  display: block;
  list-style: none;
  width: 20px;
  height: 20px;
  background: rgba(255, 255, 255, 0.2);
  animation: animate 10s ease-out infinite;
  bottom: -150px;

}

.circles li:nth-child(1) {
  left: 25%;
  width: 80px;
  height: 80px;
  animation-delay: 0s;
}


.circles li:nth-child(2) {
  left: 10%;
  width: 20px;
  height: 20px;
  animation-delay: 2s;
  animation-duration: 6s;
}

.circles li:nth-child(3) {
  left: 70%;
  width: 20px;
  height: 20px;
  animation-delay: 4s;
}

.circles li:nth-child(4) {
  left: 40%;
  width: 60px;
  height: 60px;
  animation-delay: 0s;
  animation-duration: 10s;
}

.circles li:nth-child(5) {
  left: 65%;
  width: 20px;
  height: 20px;
  animation-delay: 0s;
}

.circles li:nth-child(6) {
  left: 75%;
  width: 110px;
  height: 110px;
  animation-delay: 3s;
}

.circles li:nth-child(7) {
  left: 35%;
  width: 150px;
  height: 150px;
  animation-delay: 7s;
}

.circles li:nth-child(8) {
  left: 50%;
  width: 25px;
  height: 25px;
  animation-delay: 15s;
  animation-duration: 20s;
}

.circles li:nth-child(9) {
  left: 20%;
  width: 15px;
  height: 15px;
  animation-delay: 2s;
  animation-duration: 15s;
}

.circles li:nth-child(10) {
  left: 85%;
  width: 150px;
  height: 150px;
  animation-delay: 0s;
  animation-duration: 6s;
}



@keyframes animate {

  0% {
    transform: translateY(0) rotate(0deg);
    opacity: 1;
    border-radius: 0;
  }

  100% {
    transform: translateY(-1000px) rotate(720deg);
    opacity: 0;
    border-radius: 50%;
  }

}

#welcome {
  left: 30%;
  height: 90%;
  top: 3%;
  width: 80%;
}

@media (max-width: 1024px) {
  #welcome {
    left: 40%;
  }
}
</style>
<script>
$(document).ready(function() {
  welcome();
  // $('.modal-backdrop, .modal-backdrop.fade.in').click(function(e) {
  //   e.preventDefault();
  // });
});

function welcome() {
  $('#welcome').modal({
    backdrop: 'static',
    keyboard: false
  })
  $('#welcome').modal('show');
}

function gretReview(msgId,rv)
{
	$(".btnReview").attr("disabled",true);
	$.post("welcome_ajax.php",
	{
		msgId:msgId,
		rv:rv,
		user:$("#user").text().trim()
	},
	function(data,status)
	{
		alert(data);
	});
	$('#welcome').modal('hide');
}
</script>
