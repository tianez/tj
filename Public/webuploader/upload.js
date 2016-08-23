function upload($key,$formData) {
	var uploader = WebUploader.create({
		//				auto: true,// 选完文件后，是否自动上传。
		swf: './dist/Uploader.swf', // swf文件路径
		server: 'http://localhost/mdtj/home/index/uploadimg', // 文件接收服务端。
		// 选择文件的按钮。可选。
		// 内部根据当前运行是创建，可能是input元素，也可能是flash.
		//	pick: '#filePicker',
		pick: {
			id: '#filePicker_'+$key,
			multiple: false
		},

		formData: $formData,
		accept: { // 只允许选择图片文件。
			title: 'Images',
			extensions: 'gif,jpg,jpeg,bmp,png',
			mimeTypes: 'image/*'
		},

		disableGlobalDnd: true, // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
		chunked: false,
		dnd: '#dndArea_'+$key,
		compress: {  //压缩
			width: 200,
			height: 200,
			crop:true,
		},
		fileNumLimit: 2,
	});
	// 拖拽时不接受 js, txt 文件。
	uploader.on('dndAccept', function(items) {
		var denied = false,
			len = items.length,
			i = 0,
			// 修改js类型
			unAllowed = 'text/plain;application/javascript ';
		for (; i < len; i++) {
			// 如果在列表里面
			if (~unAllowed.indexOf(items[i].type)) {
				denied = true;
				break;
			}
		}
		return !denied;
	});
	//创建缩略图
	uploader.on('fileQueued', function(file) {
		$files = uploader.getFiles();
		$length = $files.length;
		if ($length > 1) {
			$length = $length - 2;
			uploader.removeFile($files[$length], true);
		}
		$('#fileList_'+$key).empty();

		uploader.md5File(file)

		// 及时显示进度
		.progress(function(percentage) {
			console.log('Percentage:', percentage);
		})

		// 完成
		.then(function(val) {
			console.log('md5 result:', val);
		});

		var $li = $(
				'<div id="' + file.id + '" class="file-item">' +
				'<img>' +
				'<div class="info">' + file.name + '</div>' +
				'</div>'
			),
			$img = $li.find('img');
		// $list为容器jQuery实例
		console.log(file.size);
		console.log(file.lastModifiedDate );
		console.log( WebUploader.formatSize(file.size) );    // => 1024MB

		$('#fileList_'+$key).append($li);
		// 创建缩略图
		// 如果为非图片文件，可以不用调用此方法。
		// thumbnailWidth x thumbnailHeight 为 100 x 100
		uploader.makeThumb(file, function(error, src) {
			if (error) {
				$img.replaceWith('<span>不能预览</span>');
				return;
			}
			$img.attr('src', src);
		}, 200, 200);
	});
	// 文件上传过程中创建进度条实时显示。
	uploader.on('uploadProgress', function(file, percentage) {
		var $li = $('#' + file.id),
			$percent = $li.find('.progress span');
		// 避免重复创建
		if (!$percent.length) {
			$percent = $('<p class="progress"><span class="progress_span"></span></p>')
				.appendTo($li)
				.find('span');
		}
		$percent.css('width', percentage * 100 + '%');
		console.log(percentage);
	});
	// 文件上传成功，给item添加成功class, 用样式标记上传成功。
	uploader.on('uploadSuccess', function(file, data) {
		$('#' + file.id).addClass('upload-state-done');
		$res = data[0];
		$res_url = $res.savepath+$res.savename;
		console.log($res_url);
		$('#'+$key).val($res_url);
		
	});
	// 文件上传失败，显示上传出错。
	uploader.on('uploadError', function(file) {
		var $li = $('#' + file.id),
			$error = $li.find('div.error');
		// 避免重复创建
		if (!$error.length) {
			$error = $('<div class="error"></div>').appendTo($li);
		}
		$error.text('上传失败');
	});
	// 完成上传完了，成功或者失败，先删除进度条。
	uploader.on('uploadComplete', function(file) {
		$('#' + file.id).find('.progress').remove();
	});

	uploader.on('uploadAccept', function(file, response) {
		console.log(response);
		console.log(response._raw);
		//  if ( hasError ) {
		//      // 通过return false来告诉组件，此文件上传有错。
		//      return false;
		//  }
	});
	return uploader;
}