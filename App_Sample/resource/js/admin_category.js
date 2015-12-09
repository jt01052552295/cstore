// 관리자 Category setting 
var admin_category = (function(){
    
    return {
        oCategoryList : {},
        aAddedProductNo : [],
        bSlideChange : false,
        bModelChange : false,
        sDepth1 : '.prdCategory1',
        sDepth2 : '.prdCategory2',
        sDepth3 : '.prdCategory3',
        sDepth4 : '.prdCategory4',
        sSearchedList : '.gProductList',
        sSearchedTotal : '.gTotal .total_record',
        sSortMethod : 'select[name="sort_method"]',
        
        // 초기화
        init : function() {
            var self = this ;
            
            self.setCategoryList();
            self.firstDepthAppend();
            self.eventBind();
            // 카테고리 분류 선택하기.
            if ( $(".choice:checked").val() == "category" ) {
                self.selectedCategory();
            }
            
        },
        
        // 카테고리 분류 선택시
        selectedCategory : function() {
            var categoryList = this.oCategoryList;
            var depth1 = "select[name='depth1']",
            depth2 = "select[name='depth2']",
            depth3 = "select[name='depth3']",
            depth4 = "select[name='depth4']",
            depth1Value = "<?php echo $settings['depth1']; ?>",
            depth2Value = "<?php echo $settings['depth2']; ?>",
            depth3Value = "<?php echo $settings['depth3']; ?>",
            depth4Value = "<?php echo $settings['depth4']; ?>";

            if ( !categoryList ) {
                return false;
            };
            
            if ( depth1Value != "") {
                $("select[name='depth1']").find("option").filter( function(i, opt){
                    if ( opt.value == depth1Value ) {
                        $(this).attr("selected", "selected");
                        if ( depth2Value != "" ) {
                            $.each( categoryList[depth1Value], function(key, value) {
                                $('<option value="'+key+'" class="item">'+value+'</option>').appendTo(depth2);
                            });
                            $(depth2).find("option").filter( function(i, opt){
                                if ( opt.value == depth2Value ) {
                                    $(this).attr("selected", "selected");
                                    if ( depth3Value != "" ) {
                                        $.each( categoryList[depth2Value], function(key, value) {
                                            $('<option value="'+key+'" class="item">'+value+'</option>').appendTo(depth3);
                                        });
                                        $(depth3).find("option").filter( function(i, opt){
                                            if ( opt.value == depth3Value ) {
                                                $(this).attr("selected", "selected");
                                                if ( depth4Value != "" ) {
                                                    $.each( categoryList[depth3Value], function(key, value) {
                                                        $('<option value="'+key+'" class="item">'+value+'</option>').appendTo(depth4);
                                                        $(depth4).find("option").filter( function(i, opt){
                                                            if ( opt.value == depth4Value ) {
                                                                $(this).attr("selected", "selected");
                                                            };
                                                        });
                                                    });
                                                };
                                            };
                                        });
                                    };
                                };
                            });
                        };
                    };
                });
            };
        },
        
        // 카테고리 분류별 배열 저장
        setCategoryList : function() {
            var self = this;
            <?php
                function _CategoryScript($ParentNo, $aData) {
                    echo 'self.oCategoryList["'.$ParentNo.'"] = {};';
                    foreach ($aData as $No => $Data) {
                        echo 'self.oCategoryList["'.$ParentNo.'"]["'.$No.'"] = "'. str_replace("\"", "",$Data['categoryName']) .'";';
                        _CategoryScript( $No, $Data['childData']);
                    }
                }

                if ( $CategoryData ) {
                    _CategoryScript(0, $CategoryData);
                }
            ?>
        },

        firstDepthAppend : function () {
            var self = this;

            if ( self.oCategoryList[0] ) {
                $.each(self.oCategoryList[0], function(key, value) {
                    $('<option value="'+key+'" class="item">'+value+'</option>').appendTo($(self.sDepth1));
                });                
            }
        },   
        
        // 카테고리 선택시 상품이 없을경우 메시지 처리
        productNothing : function(){
            var self = this;
            $(self.sSearchedList).empty();
            $('<tr><td colspan="3" class="noData">검색된 상품이 없습니다.</td></tr>').appendTo(self.sSearchedList);
        },
        
        // 카테고리 
        categorySelectClear : function (el) {
            $(el).find('.item').remove();
        },
        
        // 가격정보의 , 단위 설정
        setComma : function ( num ) {
            return Number(num).toLocaleString().split(".")[0]
        },
        
        // 카테고리 선택시 이벤트 발생 처리
        eventBind : function() {
            var s = this;
            
            $(s.sDepth1).change( function() {
                var iParentNo = this.value;
                s.categorySelectClear( s.sDepth2 );
                if ( iParentNo ) {
                    $.each( s.oCategoryList[iParentNo], function(key, value) {
                        $('<option value="'+key+'" class="item">'+value+'</option>').appendTo(s.sDepth2);
                    });
                };
                s.categorySelectClear(s.sDepth3);
                s.categorySelectClear(s.sDepth4);
                s.categoryProductRequest();
            });
            
            $(s.sDepth2).change( function() {
                var iParentNo = this.value;
                s.categorySelectClear( s.sDepth3 );
                if ( iParentNo ) {
                    $.each( s.oCategoryList[iParentNo], function(key, value) {
                        $('<option value="'+ key +'" class="item">'+value+'</option>').appendTo(s.sDepth3);
                    });
                };
                s.categorySelectClear(s.sDepth4);
                s.categoryProductRequest();
            });
            
            $(s.sDepth3).change( function() {
                var iParentNo = this.value;
                s.categorySelectClear( s.sDepth4 );
                if ( iParentNo ) {
                    $.each( s.oCategoryList[iParentNo], function(key, value) {
                        $('<option value="'+key+'" class="item">'+value+'</option>').appendTo(s.sDepth4);
                    });
                };
                s.categoryProductRequest();
            });
            
            // 추천 제외 상품으로 인한 상품명 검색 추가.
            $(".btnSearchName").bind("click", function(){
                searchNameSubmit();
            });
            
            function searchNameSubmit() {
                var search_name = $("#search_name").val();
                if ( search_name == "" ) {
                    alert( "상품명을 입력하세요.");
                    $("#search_name").focus();
                    return;
                }
                $.getJSON( '[LINK=api/ProductName]',{'search_name':search_name}, function(req){
                    if ( req.Data && req.Data.data.total_record > 0 ) {
                        s.categoryDataBind( req.Data.data );
                        $(s.sSearchedTotal).html( req.Data.data.products.length );
                    } else {
                        s.productNothing();
                    }
                });
            }
            
            // 검색 키워드 입력창에서 엔터(return, enter) 입력시 submit처리
            $("#search_name").bind("keydown", function(e){
                if ( e.keyCode == 13 || e.which == 13) {
                    searchNameSubmit();
                    return false;
                } 
            });

            // 카테고리 select box 선택 변경시 다음 카테고리 분류 정보 호출
            $("select[name='sort_method']").bind("change", function(){
                s.categoryProductRequest();
            });
        },
        
        // 해당 카테고리에 있는 상품 정보 호출
        categoryProductRequest : function ( gSearchText ) {

            if ( $(".choice:checked").val() == "category" ) {
                return false;
            };
            
            var self = this,
            iCategoryNo = 0,
            sortMethod =  $(self.sSortMethod).val();
    
            for ( var iDepthNum = 1; iDepthNum < 5; iDepthNum++ ){
                var depthValue = $('.select-product .prdCategory'+iDepthNum).val();
                if ( depthValue != "" ) {
                    iCategoryNo = depthValue;
                }
            }
            
            $.getJSON( '[LINK=api/CategoryProduct]',{'categoryNo':iCategoryNo, 'sortMethod':sortMethod}, function(req){
                if ( req.Data && req.Data.total_record > 0 ) {
                    self.categoryDataBind( req.Data );
                } else {
                    self.productNothing();
                }
            });
        },
        
        // ajax 요청후 카테고리 데이터 바인드.
        categoryDataBind : function( mCategoryProductResult ) {
            var self = this;
            if ( mCategoryProductResult ) {
                var $SearchedList = $(self.sSearchedList);
                
                $SearchedList.find('a.icoPreview').unbind('click');
                $SearchedList.find('a.rowName').unbind('click');
                $SearchedList.find('tr').remove();
                
                if ( mCategoryProductResult.total_record > 0 ) {
                    $(self.sSearchedTotal).html(mCategoryProductResult.total_record);

                    if ( mCategoryProductResult.products && mCategoryProductResult.products.length < 1 ) {
                        self.productNothing();
                        return;
                    };
                    $.each(mCategoryProductResult.products, function(key, value) {
                        var imgSrc = value.prd_img_tiny || "";
                      
                        if(  !imgSrc.match(/\.gif|\.png|\.jpg|\.jpeg/i) ) {
                            imgSrc = value.prd_img_small;
                        };
                        
                        if(  !imgSrc.match(/\.gif|\.png|\.jpg|\.jpeg/i) ) {
                            imgSrc = value.prd_img_medium;
                        };
                        
                        if ( imgSrc && !imgSrc.match(/\.gif|\.png|\.jpg|\.jpeg/i) ) {
                            imgSrc = "http://img.echosting.cafe24.com/smartAdmin/img/common/@img_38x38.jpg";
                        }else{
                            imgSrc = "http://"+$('#mall_id').val()+".cafe24.com/" + imgSrc;
                        }
                        
                        $('<tr class="rows" id="row-' + value.prd_no + '">' +
                                '<input type="hidden" class="rowPrice" value="' + value.prd_price + '">' +
                                '<td class="chk"><input type="checkbox" class="rowCk rowNo" name="addProductNo[]" value="'+value.prd_no+'"></td>' +
                                '<td>' +
                                '<div class="goods type1">' +
                                '<span class="frame"><img class="rowImg" alt="" src="'+imgSrc+'" width="38px" height="38px"></span>' +
                                '<p><a href="javascript:;" class="rowName">'+value.prd_name+'</a></p>' +
                                '<p class="desc rowDesc">' + self.setComma(value.prd_price) + '원</p>' +
                                '<span class="preview"><a href="javascript:;" class="icoPreview" title="미리보기">미리보기</a></span>' +
                                '</div>' +
                                '</td>' +
                        '</tr>').appendTo($SearchedList);
                    }); 
                    $SearchedList.find('a.icoPreview').bind('click', function(e) {
                        var product_no = $(this).closest('tr').find('.rowCk').val();
                        var url = '/surl/P/' + product_no;
                        window.open(url, 'newWin' );
                        return false;
                    });
                    $SearchedList.find('a.rowName').bind('click', function() {
                        var product_no = $(this).closest('tr').find('.rowCk');
                        if ( product_no.attr("checked") ) {
                            product_no.removeAttr("checked");
                        } else {
                            product_no.attr("checked", "checked");
                        }
                    });
                }; 
            };
        }

    };

}());