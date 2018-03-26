$(function(){
	//后台管理员登录
	$('#login_sub').click(function(){
		if($('#login_user').val()!='' && $('#login_pass').val()!=''){
			var user=$('#login_user').val();
			var pwd=$('#login_pass').val();
			var flag=0;
			$.ajax({
				type:'POST',
				url:conUrl+'/login',
				data:{'user':user,'password':pwd},
				success:function(data){
					if(data.flag==1){
						location.href="/index/Admin/admin";
					}else if(data.flag==0){
						alert('用户名或密码错误！');
					}
				},
				async:false
			});
			if(flag==0){
				return false;
			}
		}else{
			alert('账号密码不得为空！');
			return false;
		}
	})
	$('#login_pass').keydown(function(e){
		if(e.keyCode==13){
			$('#login_sub').click();
		}
	})
	//出入金提示
	var deposit_music = document.getElementById("deposit_music");
	if($('#deposit').html()){
		var deposit=$('#deposit').html();
		if(deposit>0){
			$('#rujin_bell').show();
		}else{
			$('#rujin_bell').hide();
		}
		refreshOnTime();
		setInterval(refreshOnTime,3000);
		function refreshOnTime(){
			$.ajax({
				type:'POST',
				url:conUrl+'/refresh',
				data:{'time':3},
				success:function(data){
					$('#deposit').html(data.deposit);

					if(data.deposit>0){
						$('#rujin_bell').show();
						deposit_music.play();
					}else{
						$('#rujin_bell').hide();
						deposit_music.pause();
					}

				}
			});
			
		}
	};

	$('#to_deposit').click(function(){
		$(parent.frames["left"].document).find("#left ul li a").removeClass('active');
		$(parent.frames["left"].document).find("#in_deposit").addClass('active');
	})
	//左侧选择
	$('#left h3').click(function(){
		var sumWidth =1;
		$(this).parent().find("li").each(function(){
			sumWidth += $(this).height()+1;
		});
		if($(this).parent().find('ul').height()!=0){
			$(this).parent().find('ul').stop().animate({'height':0});
			$(this).find('span').css({'transform':'rotateZ(180deg)'});
		}else{
			$('#left h3').parent().find('ul').stop().animate({'height':0});
			$('#left h3').find('span').css({'transform':'rotateZ(180deg)'});
			$(this).parent().find('ul').stop().animate({'height':sumWidth});
			$(this).find('span').css({'transform':'rotateZ(0deg)'});
		}
	})
	
	$('#left li a').click(function(){
		$('#left li a').removeClass('active');
		$(this).addClass('active');
	})
	
	$('.had_click').click();
	//阻止默认事件
	$('.stop_default').click(function(e){
		e.stopPropagation();
	})
    $('#success_deposit').click(function(){
        var out_orderid=$('#deposit_out_orderid').val();
        var id=$('#deposit_id').val();
		$.ajax({
            type:'post',
            url:'/goods/order/deposit_success',
            data:{'out_orderid':out_orderid,'success':id},
            success:function (data) {
                if(data.res=='1'){
                    alert('处理成功');
                    location.href=conUrl+"/sq_order";

                }

            }
        })

    })
})