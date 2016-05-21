form_responses["createNewsPost"] = function(data) {
    if(is_string(data)) {
        alert(data);
    } else {
        navigate("/news?id="+data.id);
    }
}

form_responses["editNewsPost"] = function(data) {
    if(is_string(data)) {
        alert(data);
    } else {
        navigate("/news?id="+data.id);
    }
}