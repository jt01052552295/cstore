$(document).ready(function(){

    var $btnLarge = $("#btnLargeSave"), $form = $("#main_image_form"), f = $form.get(0);

    // 업로드 버튼 클릭시
    $btnLarge.click(function(){

        if ( f.imgFile.value === "") {
                alert(' 업로드할 파일을 선택하셔야합니다.');
                return false;
        }

        // 파일 업로드 SDKJS 실행
        [SDKJS]
        var fCallback = function(){};
        Cafe24_SDK_Upload_Submit( $form, {'callback':fCallback});

        return false;
    });
});
