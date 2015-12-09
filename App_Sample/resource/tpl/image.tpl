<div class="section">
    <div class="mTitle">
        <h2>이미지 등록</h2>
    </div>

    <p class="mRequired"><strong class="txtMust">*</strong> 필수 입력사항</p>

    <div class="mBoard type2 gSmall">

        <form name="main_image_form" id="main_image_form" action="[link=ImageOk]" method="post"  enctype="multipart/form-data">
        <input type="hidden" name="act" value="insert"  />
        <table border="1" summary="">
        <caption>이미지 등록</caption>
        <tbody>
        <tr>
            <th scope="row">이미지 <strong class="txtMust">*</strong></th>
            <td>
                <ul class="gSelectList">
                    <li>
                        <div class="addInput"  id="display-img-type-U">
                            <p><input type="file" name="imgFile" size="55" class="fFile" style="width:480px;" /></p>
                            <br/>

                            <?php if ( !empty($pc_upload_path)){?>
                            <img src="<?=$pc_upload_path;?>"/>
                            <?php }?>
                        </div>
                    </li>
                </ul>
            </td>
        </tr>
        </tbody>
        </table>
        </form>
        <span class="err"><?php echo $errors['exec'] ?> </span>
    </div>
</div>

<div class="mButton">
    <p>
        <a href="javascript:;" id="btnLargeSave" class="btnSubmit"><span>업로드</span></a>
        <a href="[link=admin/index]" class="btnCancel"><span>취소</span></a>
    </p>
</div>

