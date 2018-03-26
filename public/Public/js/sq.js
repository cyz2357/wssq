/**
 * Created by Administrator on 2017/12/23.
 */
$(function () {

    $('#subbut').click(function () {

        if($('.tb-selected a').text()==''){
            alert('请选择套餐产品');
            return false;
        }
        var pattern = /^[\u4E00-\u9FA5]{1,6}$/;
        var pattern1 = /^1[34578]\d{9}$/;
        var regexp=/^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/;

        if(!pattern.test($('#consignee').val())){
            alert('请输入中文姓名');
            return false;
        }
        if(!pattern1.test($('#mobile').val())){
            alert('请输入正确的手机号码');
            return false;
        }
        if($("#s_province[name=s_province]").find("option:selected").text()=='省份'||$("#s_city[name=s_city]").find("option:selected").text()=='地级市'||$("#s_county[name=s_county]").find("option:selected").text()=='市、县级市'){
            alert('请选择省、市、地区');
            return false;
        }
        if($('#address').val()==''){
            alert('请输入详细的地址，以方便我们邮寄，谢谢您的配合!');
            return false;
        }
        if($('#gddh').val()!=''&&!regexp.test($('#gddh').val())){
            alert('请输入正确的固定电话!!');
            return false;
        }

        var goods_name=$('.tb-selected a').text();
        var num=$('#p_count').val();
        var amount=$('#youhuijia').text();
        var name=$('#consignee').val();
        var phone=$('#mobile').val();
        var gddh=$('#gddh').val();
        var time=$("input[type=radio][checked]").val();
        var pay=$("input[name=pay_id][checked]").parent('label').text();
        var message=$('#user_note').val();
        var out_orderid=$('#out_orderid').val();
        var address=$('#address').val();
        var province=$("#s_province[name=s_province]").find("option:selected").text();
        var city=$("#s_city[name=s_city]").find("option:selected").text()
        var county=$("#s_county[name=s_county]").find("option:selected").text();
        $.ajax({
            url:'/index/index/goods',
            type:'post',
            data:{'county':county,'city':city,'province':province,'address':address,'out_orderid':out_orderid,'message':message,'goods_name':goods_name,'num':num,'amount':amount,'name':name,'phone':phone,'gddh':gddh,'time':time,'pay':pay,},
            success:function (data) {
                if(data.res=='1'){
                    alert('订单提交成功');
                }
            }
        })
    })
    $('#paya1').click(function () {
        $(this).attr('checked',true);
        $('#paya2').attr("checked",false);

    })
    $('#paya2').click(function () {
        $(this).attr('checked',true);
        $('#paya1').attr("checked",false);

    })

})