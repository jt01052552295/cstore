
<div class="section">
    <div class="mTitle">
        <h2>사용 설정</h2>
    </div>

    <form name="settingForm" id="settingForm" method="post" action="[link=admin/indexok]" enctype="multipart/form-data" >
    <input type="hidden" name="mall_id" id="mall_id" value= "<?php echo $mall_id?>" />
    <input type="hidden" name="p_nos" id="p_nos" value="<?php echo $p_nos?>" />
    <div class="mBoard type2 gSmall">
        <table border="1" summary="">
        <caption>사용 설정</caption>
        <tbody>
            <tr>
                <th scope="row">상품 출력 방식</th>
                <td>
                    <label class="fChk">
                        <input type="radio" name="pChoice" class="choice" value="category" <?php if ($pChoice === 'category' ) { echo "checked='checked'"; }?> <?php echo $defaultPchoice ?> />카테고리 선택
                    </label>                
                    <label class="fChk">
                        <input type="radio" name="pChoice"  class="choice" value="product" <?php if ($pChoice === 'product' ) { echo "checked='checked'"; }?>  /> 상품 지정
                    </label>
                </td>
            </tr>
        </tbody>
        </table>
    </div>
   

    <div class="mTitle gSub">
        <h3>기본 출력 상품</h3>
    </div>
    
    <p class="mRequired"><strong class="txtMust">*</strong> 필수 입력사항</p>
    
    <div class="mBoard type2 gSmall select-category <?php if( $pChoice == "product" ) { echo "displaynone"; } ?> ">
    <table border="1" summary="">
        <caption>상품정보 설정</caption>
        <tbody>
            <tr>
                <th scope="row">상품 노출 개수 <strong class="txtMust">*</strong></th>
                <td>
                    <select name="rows">
                        <?php
                            if ( empty($rows) ) {
                                $rows = 3;
                            }
                            for ( $i=10; $i >=1; $i--) {
                                $sSelected = "";
                                if ( $rows == $i ) {
                                    $sSelected = "selected='selected'";
                                }
                                echo "<option value='" . $i . "' " .$sSelected . " >" . $i . "</option>";
                            }
                        ?>
                    </select> 개
                </td>
            </tr>        
        <tr class="">
            <th scope="row">상품카테고리 <strong class="txtMust">*</strong></th>
            <td class="prdCategory category">
                <select class="prdCategory1" name="depth1" >
                    <option value="">대분류 선택</option>
                </select>
                <select class="prdCategory2" name="depth2">
                    <option  value="">중분류 선택</option>
                </select>
                <select class="prdCategory3" name="depth3" >
                    <option  value="">소분류 선택</option>
                </select>
                <select  class="prdCategory4" name="depth4" >
                    <option  value="">세분류 선택</option>
                </select>
            </td>
        </tr>
        </tbody>
        </table>
        <br />
    </div>
    </form>

    <div class="pickerArea gLookBook select-product <?php if( $pChoice == "category" ) { echo "displaynone"; } ?>  <?php echo $defaultDisplay?>" >
        <div class="mPicker gFlow">
            <h2>상품검색</h2>

            <div class="mBoard type2 gSmall" style="height:140px;">
                <table border="1" summary="">
                <caption>상품검색</caption>
                <colgroup>
                    <col width="24%">
                    <col width="auto">
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row">상품카테고리</th>
                    <td class="prdCategory">
                        <select class="prdCategory1">
                            <option value="">대분류 선택</option>
                        </select>
                        <select class="prdCategory2">
                            <option  value="">중분류 선택</option>
                        </select>
                        <select class="prdCategory3">
                            <option  value="">소분류 선택</option>
                        </select>
                        <select  class="prdCategory4">
                            <option  value="">세분류 선택</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">상품명 검색</th>
                    <td>
                    <input type="text" name="search_name" id="search_name" value="" class="fText" style="width:180px" />
                    <a href="javascript:;" class="btnEm btnSearchName"><span><em class="icoSearch"></em> 검색</span></a>
                    </td>
                </tr>
                </tbody>
                </table>
            </div>

            <div class="mResult" >
                <p class="gTotal">검색결과 <em class="total_record">0</em>개</p>
            </div>



            <div class="gTableMerge">
                <div class="mBoard type1 typeHead">
                    <table border="1" class="eTr" summary="">
                    <caption>상품목록</caption>
                    <colgroup>
                        <col class="chk">
                        <col style="width:auto;">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th class="chk" scope="col"><input type="checkbox" class="allCk"></th>
                        <th scope="col">상품명</th>
                    </tr>
                    </tbody>
                    </table>
                </div>

                <div class="mBoard type1 typeBody">
                    <table border="1" class="eTr" summary="">
                    <caption>상품검색</caption>
                    <colgroup>
                        <col class="chk">
                        <col style="width:auto;">
                    </colgroup>
                    <tbody class="gProductList center">
                    <tr>
                        <td class="noData" colspan="2">검색된 상품이 없습니다.</td>
                    </tr>
                    </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="mAddBtn">
            <p>
                <span class="button"><button class="btnAdd">추가</button></span>
            </p>
        </div>

        <div class="mPicker gReverse">
            <h2>상품 진열 목록</h2>

            <div class="mResult">
                <p class="gTotal">진열중 상품 <em><?php echo count($defaultProducts) ?></em>개</p>
            </div>

            <div class="mCtrl typeDisplay">
                <p class="gCtrlLeft">
                    <button class="btnMove icoFirst"><span>선택한 항목 최상단으로 이동</span></button>
                    <button class="btnMove icoPrev"><span>선택한 항목 한줄 위로 이동</span></button>
                    <button class="btnMove icoNext"><span>선택한 항목 한줄 아래로 이동</span></button>
                    <button class="btnMove icoLast"><span>선택한 항목 최하단으로 이동</span></button>
                </p>
                <div class="gCtrlRight">
                    <a class="btnEm selectedDel" href="javascript:;"><span><em class="icoDel"></em> 삭제</span></a>
                </div>
            </div>

            <div class="gTableMerge">
                <div class="mBoard type1 typeHead">
                    <table border="1" class="eTr" summary="">
                    <caption>슬라이드 진열목록</caption>
                    <colgroup>
                        <col class="chk">
                        <col class="array">
                        <col width="auto">
                    </colgroup>
                    <tbody>
                    <tr class="">
                        <th class="chk" scope="col"><input type="checkbox" class="allCk"></th>
                        <th class="array" scope="col">순서</th>
                        <th scope="col">상품명</th>
                    </tr>
                    </tbody>
                    </table>
                </div>

                <div class="mBoard type1 typeBody">
                    <table border="1" class="eTr" summary="">
                    <caption>상품검색</caption>
                    <colgroup>
                        <col class="chk">
                        <col class="array">
                        <col width="auto">
                    </colgroup>
                    <tbody class="center">
                    <?php
                        if ( count($defaultProducts) > 0 ) {
                            foreach ( $defaultProducts as $p ) {
                    ?>
                        <tr id="row-<?php echo $p['product_no']?>" class="rows">
                            <input type="hidden" value="<?php echo $p['price']?>" class="rowPrice">
                            <td class="chk"><input type="checkbox" value="<?php echo $p['product_no']?>" name="addProductNo[]" class="rowCk rowNo"></td>
                            <td class="array"><input type="text" readonly="" style="border:0;" class="fText order" name="inOrder[]"></td>
                            <td>
                                <div class="goods type1">
                                    <span class="frame"><img width="38px" height="38px" src="<?php echo $p['img_url']?>" alt="" class="rowImg"></span>
                                    <p><a class="rowName" href="javascript:;"><?php echo $p['title']?></a></p>
                                    <p class="desc rowDesc"><?php echo number_format($p['price'])?>원</p>
                                    <span class="preview"><a title="미리보기" class="icoPreview" href="javascript:;">미리보기</a></span>
                                </div>
                            </td>
                        </tr>
                    <?php
                            }
                        } else {
                    ?>
                    <tr class="">
                        <td class="noData" colspan="3">진열된 상품이 없습니다.</td>
                    </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                    </table>
                </div>
            </div>

            <div class="mCtrl typeDisplay">
                <p class="gCtrlLeft">
                    <button class="btnMove icoFirst"><span>선택한 항목 최상단으로 이동</span></button>
                    <button class="btnMove icoPrev"><span>선택한 항목 한줄 위로 이동</span></button>
                    <button class="btnMove icoNext"><span>선택한 항목 한줄 아래로 이동</span></button>
                    <button class="btnMove icoLast"><span>선택한 항목 최하단으로 이동</span></button>
                </p>
                <div class="gCtrlRight">
                    <a class="btnEm selectedDel" href="javascript:;"><span><em class="icoDel"></em> 삭제</span></a>
                </div>
            </div>

            <div class="mCtrlInsert">
                <span>간단순서설정 :</span>
                선택항목을
                <input type="text" class="fText" id='movePosition'>
                번 위치로 이동합니다.
                <a class="btnEm selectedMove" href="javascript:;"><span><strong>이동</strong></span></a>
            </div>
        </div>
    </div>
</div>

<div class="mButton">
    <p>
        <a class="btnSubmit" href="javascript:;" id='newSubmit' ><span>저장</span></a>
        <a class="btnCancel" href="javascript:history.back(-1);"><span>취소</span></a>
    </p>
</div>

