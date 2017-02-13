function checkEmail (email) {
	re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
	if(re.test(email)){
		return true;
	}
	alert("Неправильно введен email");
	return false;
}

function checkPhone (phone) {
	re = /^\d[\d\(\)\ -]{4,14}\d$/;
	if(re.test(phone)){
		return true;
	}
	alert("Неправильно введен телефон");
	return false;
}

$(document).ready(function() {

	function checkAll() {
		email = $("#email").val();
		phone = $("#phone").val();
		fio = $("#fio").val();
		comment = $("#comment").val();
		if(email && phone && fio && comment){
			return true;
		}
		return false;
	}

	var domain = "http://algorithm.test";

	$('#sub').click(function(event) {
		event.preventDefault();
		if (checkPhone($("#phone").val()) && checkEmail($("#email").val()) && checkAll()) {
			dat = {
				email : $("#email").val(), 
				phone : $("#phone").val(), 
				fio : $("#fio").val(), 
				comment : $("#comment").val()
			};
			$.ajax({
				type: "POST",
				url: domain + '/core/index.php',
				dataType: 'json',
				data: dat,
				success: function(data) {
					if (data.done) {
						alert(data.message);
					}
				}
			});
		}
	});
})



