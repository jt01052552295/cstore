var admin_setting = (function(){
    
    var options = {
        'addBtn' : '.btnAdd',
        'sSearchedList' : '.gProductList', 
        'sAddedList' : '.gReverse .typeBody table',
        'sAddedTotal' : '.gReverse .mResult .gTotal em',
        'delBtn' : '.gReverse .selectedDel',
        'moveBtn' : '.gReverse .selectedMove' ,
        'checkList' : '.gReverse .rowCk:checked' ,
        'tbody' : '.gReverse .typeBody tbody' ,
        'firstBtn' : '.icoFirst',
        'lastBtn' : '.icoLast' ,
        'prevBtn' : '.icoPrev',
        'nextBtn' : '.icoNext'
    };
    
    var aAddedProductNo = [];
    
    return {

        init : function ( opts ) {
            var s= this;
            s.opts = $.extend( {}, options, opts );
            s.setAddedProductNo();
            s.productAllCheck();
            s.bindAddList();
            s.bindDeleteList();
            s.bindAddLink();
            s.resetNumAddedProduct();
            s.moveInputPosition();
            s.moveFirstPosition();
            s.moveLastPosition();
            s.movePrevPosition();
            s.moveNextPosition();
        },
        
        // 상품 번호 배열에 저장
        setAddedProductNo : function () {
            var self = this;
            <?php
                if ( count($addProductNum) > 0 ) {
                    foreach ($addProductNum as $iProductNo) {
                        echo "var iProductNo = '" .$iProductNo."'; ";
                        echo "aAddedProductNo.push( iProductNo.toString() ); ";
                    }
                }
            ?>
        },
        
        // 상품 checkbox 전체 선택시(토글 방식)
        productAllCheck : function () {
            var s = this;
            $('.gFlow .allCk').click(function() {
                $(s.opts.sSearchedList).find('.rowCk').attr('checked', $(this).attr('checked'));
            });
            
            $('.gReverse .allCk').click(function() {
                $(s.opts.sAddedList).find('.rowCk').attr('checked', $(this).attr('checked'));
            }); 
        },
        
        // 상품 진열에서 삭제
        bindDeleteList : function () {
            var s = this;
            $( s.opts.delBtn ).click(function() {
                var $checkedList = $(s.opts.checkList);
                if( $checkedList.length == 0 ) {
                    alert( '삭제하실 상품을 선택하셔야 합니다.')
                    return false;
                }
                $checkedList.each(function(){
                    var $this = $(this);
                    var aProductNoList = aAddedProductNo;
                    var iProductNo = $this.val();
                    var iThisProductNoIndex = $.inArray(iProductNo, aProductNoList)
                    $this.parent().parent().remove();
                    aProductNoList.splice( iThisProductNoIndex, 1 );
                });
                s.resetNumAddedProduct();
                if( $(s.opts.sAddedList).find('tr.rows').length <= 0 ){
                    s.noneProduct();
                };
            });
        },
        
        // 상품 진열로 상품 추가
        bindAddList : function() {
            var s = this;
            $( s.opts.addBtn ).bind('click', function(e) {
                e.preventDefault();
                var $checkedList = $('.gFlow .rowCk:checked');
                if ( $checkedList.length > 0 ) {
                    $checkedList.each(function(){
                        if ( $(s.opts.sAddedList).find("tr").length >= 10 ) {
                            alert("10개를 초과할 수 없습니다.");
                            return false;
                        }
                        var $this = $(this),
                        iProductNo = $this.val(),
                        bAlreadyAddedCehck = $.inArray(iProductNo, aAddedProductNo);
                        
                        var rowsNo = [];
                        $.each( $(s.opts.sAddedList).find('tr.rows'), function(){
                            rowsNo.push( $(this).attr("id").replace(/row-/,'') );
                        });
                        
                        if ( $.inArray(iProductNo, rowsNo) >= 0 ) {
                            return;
                        };
                        
                        if ( bAlreadyAddedCehck >= 0 ) { //배열에 값이 있을때
                            return;
                        };
                        
                        aAddedProductNo.push( iProductNo );
                        
                        var $addProduct = $this.attr('checked','').parent().parent().clone(true);
                        var sOrderNum = "<td class='array'><input type='text' name='inOrder[]' class='fText order' style='border:0;' readonly></td>";
                        
                        $addProduct.find('td:first').after(sOrderNum);
                        
                        if ( $(s.opts.sAddedList).find('.noData').length > 0 ) {
                            $(s.opts.sAddedList).empty();
                        };
                        
                        $(s.opts.sAddedList).append($addProduct);
                        $this.attr('checked','checked')
                    });
                    
                    s.resetNumAddedProduct();
                };
            });
        },
        
        // 상품 갯수 초기화
        resetNumAddedProduct : function () {
            var s = this;
            var iSelectedProductCnt = 0;
            $.each( $(s.opts.sAddedList).find('tr.rows'), function(index, element) {
                iSelectedProductCnt++;
                $(this).find('input.order').val(iSelectedProductCnt);
            });
            $(s.opts.sAddedTotal).html(iSelectedProductCnt);
        },
        
        // 상품이 없을 경우 출력 메시지
        noneProduct : function() {
            $(this.opts.tbody).html("<tr><td colspan='3' class='noData'>진열된 상품이 없습니다.</td></tr>");
        },
        
        // 진열 목록에서 순서설정시 
        moveInputPosition : function () {
            var s = this;
            $(s.opts.moveBtn).click(function() {
                var $checkedList = $(s.opts.checkList);
                
                if ( $checkedList.length < 1 ) {
                    alert( '이동하실 상품을 선택 하셔야합니다.');
                    return false;
                };
                
                if ( $checkedList.length > 1 ) {
                    alert('하나의 상품만 선택 해 주세요');
                    return false;
                };
                
                if ( $('#movePosition').val()  == "" ) {
                    alert('이동하실 번호를 입력하셔야 합니다.');
                    return false;
                };
                
                if ( $('#movePosition').val().match(/[a-zA-Z]/ig) ) {
                    alert( '숫자만 입력하여셔야 합니다.');
                    return false;
                }
                    
                var $thisProduct = $checkedList.parent().parent();
                var iArrayOrder = $thisProduct.find('.order').val();
                var iMoveNum = parseInt( $('#movePosition').val(), 10 );

                var iAddedProductTotal = $(s.opts.sAddedTotal).html(); // 리버스토탈로~~~~
                
                var aAddedList = $(s.opts.sAddedList).find('tr');
                
                if (iMoveNum >= 1 && iMoveNum < iAddedProductTotal && iMoveNum !=iArrayOrder) {
                    $thisProduct.remove();
                    $(aAddedList[iMoveNum -1]).before($thisProduct);
                } else if ( iMoveNum == iAddedProductTotal && iMoveNum !=iArrayOrder) {
                    $thisProduct.remove();
                    $(aAddedList[iMoveNum -1]).after($thisProduct);
                }

                s.resetNumAddedProduct();
            });
        },
        
        // 진열 목록에서 맨 처음으로 이동처리
        moveFirstPosition : function () {
            var s = this;
            $( s.opts.firstBtn ).click(function(event) {
                event.preventDefault();
                var $checkedList = $(s.opts.checkList).get().reverse();
                
                if ( $checkedList.length < 1 ) {
                    alert( '이동하실 상품을 선택 하셔야합니다.');
                    return false;
                };
                
                $($checkedList).each(function(){
                    var $this = $(this);
                    var $thisProduct = $this.parent().parent();
                    $thisProduct.remove();
                    $(s.opts.sAddedList).find('tr').first().before($thisProduct);
                });
                s.resetNumAddedProduct();
            });
            
        },
        
        // 진열 목록에서 맨 마지막으로 이동처리
        moveLastPosition : function () {
            var s = this;
            $( s.opts.lastBtn ).click(function(event){
                event.preventDefault();
                var $checkedList = $(s.opts.checkList);
                
                if ( $checkedList.length < 1 ) {
                    alert( '이동하실 상품을 선택 하셔야합니다.');
                    return false;
                };
                
                $checkedList.each(function(){
                    var $this = $(this);
                    var $thisProduct = $this.parent().parent();
                    $(s.opts.sAddedList).append($thisProduct);
                });
                
                s.resetNumAddedProduct();
            });
        }, 
        
        // 진열 목록에서 이전으로 이동처리
        movePrevPosition : function () {
            var s = this;
            $(s.opts.prevBtn).click( function(event) {
                
                event.preventDefault();
                var $checkedList = $(s.opts.checkList);
                
                if ( $checkedList.length < 1 ) {
                    alert( '이동하실 상품을 선택 하셔야합니다.');
                    return false;
                };
                
                $checkedList.each(function(){
                    var $this = $(this);
                    var $thisProduct = $this.parent().parent();
                    $PrevProduct = $thisProduct.prev();
                    if ( $PrevProduct.length == 0 || $PrevProduct.find('.rowCk:checked').length > 0)
                        return true;
                    $thisProduct.remove();
                    $PrevProduct.before($thisProduct);
                });
                
                s.resetNumAddedProduct();
            });
        },
        
        // 진열 목록에서 다음으로 이동처리
        moveNextPosition : function () {
            var s = this;
            $(s.opts.nextBtn).click(function(event) {
                event.preventDefault();
                var $checkedList = $(s.opts.checkList).get().reverse();
                
                if ( $checkedList.length < 1 ) {
                    alert( '이동하실 상품을 선택 하셔야합니다.');
                    return false;
                };
                
                $($checkedList).each(function(){
                    var $this = $(this);
                    var $thisProduct = $this.parent().parent();
                    $nextProduct = $thisProduct.next();
                    if ( $nextProduct.length == 0  || $nextProduct.find('.rowCk:checked').length > 0) {
                        return true;
                    }
                    $thisProduct.remove();
                    $nextProduct.after($thisProduct);
                });
                
                s.resetNumAddedProduct();
            });
            
        },
        
        // 상품의 미리보기 링크 연결
        bindAddLink : function () {
            var s = this;
            var $SearchedList = $(s.opts.sAddedList);
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
        }
        
    };
    
}());
