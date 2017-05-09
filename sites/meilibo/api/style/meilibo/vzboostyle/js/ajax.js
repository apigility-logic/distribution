'use strict';
    var ajaxFn={
        //礼物列表
        giftajax:function(){
            $.ajax({
                url:'/OpenAPI/V1/Gift/collection',
                type: 'get',
                jsonp: 'callback', 
                dataType: 'JSONP', 
                data:{token:User.token},
                success: function(data) {
                    var gift = {
                        giftlist: data.data,
                        DEMO:HOST_URL,
                    };
                    var html = template('giftlist', gift);
                    document.getElementById('giftBox').innerHTML = html;
                }
            });
        },
        //点击加载更多热门主播
        livegetData:function(offset,size){
            $.ajax({
                url:'/OpenAPI/V1/anchor/getAnchorListTest',
                type: 'get',
                jsonp: 'callback', 
                dataType: 'JSONP', 
                data:{token:User.token},
                success: function(reponse){
                    console.log(reponse.data);
                    var data = reponse.data;
                    var sum = data.list.length;
                    var result = '';
                    if(sum - offset < size ){
                        size = sum - offset;
                    }
                    
                    for(var i=offset; i< (offset+size); i++){
                        result+='<a href="/Show/index?roomnum='+data.list[i].curroomnum+'" class="liveShowItem roomDetail left" data-momoid="'+data.list[i].curroomnum+'">'
                                        +'<div class="roomImgWrap">'
                                            +'<img class="roomImg" onerror="this.src=\'/style/avatar/0/0_big.jpg\'"src="'+HOST_URL+data.list[i].avatar+'" alt="">'
                                        +'</div>'
                                        +'<div class="roomMsg">'
                                            +'<p class="liveShowRoom">'+data.list[i].nickname+'</p>'
                                            +'<p class="onWatchUser">'+data.list[i].online+'在看</p>'
                                        +'</div>'
                                        +'<img class="deviceIcon" src="'+HOST_URL+'/style/meilibo/vzboostyle/img/phoneIcon.png" alt="">'
                                    +'</a>';
                    }
                    $('#liveShow').append(result);
                    /*隐藏more按钮*/
                    if ( (offset + size) >= sum){
                        $(".liveButton").addClass("hide");
                    }else{
                        $(".liveButton").removeClass("hide");
                    }
                },
                error: function(xhr, type){
                    alert('Ajax error!');
                }
            });
        },
        //私密播
        getNowPrivateLimit:function(){
            $.ajax({
                type:'POST',
                url:'/OpenAPI/V1/Private/getNowPrivateLimit',
                dataType:'json',
                data:{token:User.token,uid:Anchor.id},
                success:function(rep){
                    console.log(rep);
                }
            })
        },

  
    }
