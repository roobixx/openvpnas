<?php $proxystatus = file_get_contents('/var/www/stat/proxy.connected'); ?>

<!DOCTYPE html>
<html><head><meta charset='UTF-8'>
<title>[OpenVPN] Settings</title><link rel='stylesheet' type='text/css' href='sabai.css'>
<script type='text/javascript' src='jquery-1.11.1.min.js'></script>
<script type='text/javascript' src='jquery.validate.min.js'></script>
<script type='text/javascript' src='sabaivpn.js'></script>
<script type="text/javascript">
var hidden, hide, settingsForm, settingsWindow, oldip='',limit=10,info=null,ini=false;


function Settingsresp(res){ 
	settingsWindow.innerHTML = res;
	eval(res); 
	msg(res.msg); 
	showUi(); 
	console.log('Hid the update message')
}

function proxysave(act){ 
  hideUi("Adjusting Proxy..."); 
  settingsForm.act.value=act;  
  que.drop("bin/proxy.php",Settingsresp, $("#_fom").serialize() ); 
  <?php $proxystatus = file_get_contents('/var/www/stat/proxy.connected'); ?>
  setTimeout("window.location.reload()",1000);
}


function system(act){ 
	hideUi("Processing Request..."); 
	settingsForm.act.value=act; 
	que.drop("bin/system.php",Settingsresp, $("#_fom").serialize() ); 
	setTimeout("window.location.reload()",60000);
}

function DNSupdate() {
	console.log('you clicked update')
	hideUi("Updating DNS..."); 
	que.drop("bin/dns.php",Settingsresp, $("#_fom").serialize() );

}

function DNSset() {
	<?php
  if ($dns = file('/var/www/stat/dns')) {
  echo "primary =  '";
  echo trim($dns[0]);
  echo "';\nsecondary = '" . trim($dns[1]) . "';\n";
	}
	?> 	

	typeof primary === 'undefined' || $('#primaryDNS').val(primary)		
	typeof secondary === 'undefined' || $('#secDNS').val(secondary)
}

function init(){ 
   $( function() {
	f = $('#_fom'); 
	hidden = E('hideme'); 
	hide = E('hiddentext'); 
	settingsForm = E('_fom');
	settingsWindow = E('response');
	$('.active').removeClass('active')
	$('#settings').addClass('active')
	DNSset();

     $.validator.addMethod('IP4Checker', function(value) {
       var ip = /^(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])(\.(25[0-5]|2[0-4][0-9]|[0-1][0-9]{2}|[0-9]{2}|[0-9])){3}$/;
       if (value.match(ip)) {
	 E('updateDNS').disabled = false;
	 return true;
       }
       else {
	 E('updateDNS').disabled = true;
	 return false;
       }
     }, 'DNS server must be valid IP');

     $('#_fom').validate({
       rules: {
	 primaryDNS: {
	   required: true,
	   IP4Checker: true
	 },
	 secDNS: {
	   IP4Checker: true
	 }
       }
     });
});}

function username(){ 
	if ( $('#vpnaPassword').val() == $('#vpnaPWConfirm').val() ) {
	hideUi("Updating Credentials..."); 
	que.drop("bin/auth.php",Settingsresp, $("#_fom").serialize() );
	$('#saveError').hide();
	} else {
		$('#saveError').css('display', 'inline-block');
	}
}


</script>

<body onload='init();' id='topmost'>
		<table id='container' cellspacing=0>
			<tr id='body'>    
				<td id='navi'>
          <script type='text/javascript'>navi()</script>
        </td>

        <td id='content'>
        	<div class="pageTitle">Settings</div>
        	<form id='_fom' method='post'>
        	<input type='hidden' id='_act' name='act' value='reboot'>
					<div id='vpnstats'></div>
					<div id='dhcpLease' class=''>
						<div class='section-title'>DHCP / DNS</div>
						<div class='section'>
							<input type='button' name='leaseReset' id='leaseReset' class='firstButton' value='Reset Lease' onclick='system("dhcp")'/>
						</div>
						<div class='section'>
							<table class='fields'>
								<tr>
									<td class='title'>Primary DNS</td>
									<td><input type='text' name = 'primaryDNS' id='primaryDNS'></td>
								</tr>
								<tr>
									<td class='title'>Secondary DNS</td>
									<td class='title'><input type='text' name='secDNS' id='secDNS'></td>
								</tr>
							</table>
							<input type='button' id='updateDNS' class='firstButton' value='Update' onclick='DNSupdate()'>
						</div>
					</div>
					<div id='onOff' class=''>
						<div class='section-title'>Power</div>
						<div class='section'>
							<input type='button' name='power' id='power' value='Off' class='firstButton' onclick='system("shutdown")'/>
							<input type='button' name='restart' id='restart' value='Restart' onclick='system("reboot")'/>
						</div>
					</div>
					<div class='section-title'>Change Password</div>
						<div class='section'>
							<table class='fields'>
								<tr>
									<td class='title'>New Password</td>
									<td><input type='password' name = 'vpnaPassword' id='vpnaPassword'></td>
								</tr>
								<tr>
									<td class='title'>Confirm Password </td>
									<td class='title'><input type='password' name='vpnaPWConfirm' id='vpnaPWConfirm'></td>
								</tr>
							</table>
							<input type='button' id='usernameUpdate' class='firstButton' onclick='username()' value='Update' />
							<div id='saveError'> Passwords must match.</div>
						</div>
					<br><b>
					<span id='messages'>&nbsp;</span></b>
					<pre class='noshow' id='response'></pre>
					</form>
				</td>
			</tr>
		</table>
	<div id='footer'> Copyright © 2014 Sabai Technology, LLC </div>
	<div id='hideme'>
		<div class='centercolumncontainer'>
			<div class='middlecontainer'>
				<div id='hiddentext'>Please wait...</div>
				<br>
				<center>
				<img src='images/menuHeader.gif'>
				</center>
			</div>
		</div>
	</div>
</body>
</html>
