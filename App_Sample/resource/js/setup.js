$(document).ready(function(){

    admin_category.init();
    admin_setting.init();

    // 사용설정 페이지 '저장' 클릭시
    $("#newSubmit").bind("click", function(){
        
        // 대분류 선택 필수 사항 체크
        if ( $(".choice:checked").val() == "category" ) {
            if ( $("select[name='depth1']").val() == "" ) {
                alert( '대분류를 선택하셔야 합니다.');
                return false;
            }
            $("#p_nos").val("");
            $("#settingForm").submit();
            return false;
        }

        var p_nos = [];
        
        $(".gReverse tr.rows").each(function(){
            if ( this.id ) {
                var sPId = this.id.replace('row-', '');
                p_nos.push( sPId );
            };
        });
        
        $("#p_nos").val( p_nos.join(",") );
        $("#settingForm").submit();
        return false;
       
    });
    

    // 상품 선택 방식 클릭시
    $(".choice").bind("click", function(){
        var pVal = this.value;

        
        if ( pVal === "product" ) {
            // 상품 선택일 경우 
            $( ".select-product").removeClass('displaynone');
            $( ".select-category").addClass('displaynone');
        } else {
            // 카테고리 선택일 경우
            $( ".select-product").addClass('displaynone');
            $( ".select-category").removeClass('displaynone');
        }
    });
});