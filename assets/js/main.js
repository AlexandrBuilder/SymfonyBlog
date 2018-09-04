if($(".post-change")) {
    $(".post-multy-select2").select2({
        maximumSelectionLength: 20,
        placeholder: "Select tags",
        ajax: {
            url:"/tag/json",
            success: function(result){
                console.log(result);
                return {
                    locationVal: result
                };
            },
            processResults:function (data) {
                return {
                    results:$.map(data, function (val, i) {
                        return {id:val[0], text:val[1]};
                    })
                }
            }
        },
    });
}

ajaxNew = function (postId, assessment, th) {
    $.ajax({
        url: '/assessment/new',
        data: { post_id: postId, assessment: assessment},
        method : "POST",
        success: function (data) {
            $(th).closest(".rating-box").html(data);
            stopRating();
            startRating();
        },
    });
};

ajaxDelete = function (postId, th) {
    $.ajax({
        url: '/assessment/delete',
        data: { post_id: postId},
        method : "POST",
        success: function (data) {
            $(th).closest(".rating-box").html(data);
            refreshRating();
        },
    });
};

startRating = function () {
    $(".like").click(function () {
        postId=$(this).closest(".rating").data("postId");
        assessment='like';
        ajaxNew(postId, assessment, this);
    });
    $(".dislike").click(function () {
        postId=$(this).closest(".rating").data("postId");
        assessment='dislike';
        ajaxNew(postId, assessment, this);
    });
    $(".delete").click( function () {
        postId=$(this).closest(".rating").data("postId");
        ajaxDelete(postId, this);
    });
};

stopRating = function() {
    $(".like").unbind("click");
    $(".dislike").unbind("click");
};

refreshRating = function() {
    stopRating();
    startRating();
};

if($(".rating")) {
    startRating();
};

// if($( "#select-input" ))
// $( "#select-input" ).change(function() {
//     alert( "Handler for .change() called." );
// });

if($(".filter")) {
    $("#select-input-email").select2({
        maximumSelectionLength: 20,
        placeholder: "Select email",
        ajax: {
            url:"/user/json",
            type: "POST",
            data: function(params) {
                return {
                    sub_str:params.term
                }
            },
            success: function(result){
                console.log(result);
                return {
                    locationVal: result
                };
            },
            processResults:function (data) {
                return {
                    results:$.map(data, function (val, i) {
                        return {id:val[1], text:val[1]};
                    })
                }
            }
        },
    });
}

$("#select-input-status").select2({
    maximumSelectionLength: 2
});