let hold_and_close = async  (url, data = null) => {
    data = $('#act_form').serialize()
    return $.post(url, data)
}