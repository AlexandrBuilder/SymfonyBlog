if($(".post-change")) {
    console.log($(".post-change"));
    $(".multy-select2").select2({
        maximumSelectionLength: 8,
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