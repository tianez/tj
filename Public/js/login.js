$(function() {
	jQuery.validator.addMethod("yzm_code", function(value, element, param) {
		// 正则不能写错。
		$cookie = $.cookie('yzm_code');
		$value = $.md5(value)
//		var pattern = /^([0-9]*)+$/;
		return this.optional(element) || ($cookie == $value);
	}, "验证码错误！");
	$validate = $("#signupForm").validate({
//		debug: true,
		//					submitHandler: function(form) {
		//						alert("submit!");
		//						form.submit();
		//					},
		errorPlacement: function(error, element) {
			error.appendTo(element.parent());
		},

		rules: {
			username: {
				required: true,
				minlength: 5
			},
			password: {
				required: true,
				minlength: 5,
				//							rangelength: [3, 10]
			},
			yzm: {
				required: true,
				number: true,
				yzm_code:true
			}
		},
		messages: {
			username: {
				required: "用户名不能为空！",
				minlength: "用户名不能小于5个字符",
			},
			password: {
				required: "密码不能为空！",
				minlength: "密码不能小于5个字符"
			},
			yzm: {
				required: "验证码不能为空！",
				number: "验证码必须为数字！"
			}
		},
		errorElement: "em",
		//		errorPlacement: function(error, element) {
		//			error.appendTo(element.parent());
		//		},
		//		errorLabelContainer: $("#signupForm ol.container_error"),
		//		errorElement: "li",
		success: function(element) {
			element.parent().siblings('.div_error').remove();
		},

		showErrors: function(errorMap, errorList) {
			$.each(errorList, function(i, v) {
				error = v.message;
				element = v.element.attributes.getNamedItem("id").nodeValue;
				$error = "<div class='div_error'><i class='fa fa-caret-left'></i><span>" + error + "</span><div>";
				$('#' + element).parent().siblings('.div_error').remove();
				$('#' + element).parent().parent().append($error);
			});
			this.defaultShowErrors();
		}
	});
});