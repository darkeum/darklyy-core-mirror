async function end(status, comment = null){
    return window.livewire.emit('end', {status: status, comment: comment})
}

let allCheckboxes = () => {
    let elements = $('.checks');
    for(let i=0; i < elements.length; i++) {
        elements[i].checked = elements[i].checked !== true;
    }

    return true;
}

/**
 * Обновление комментария
 *
 * @param id
 * @param comment
 * @returns {Promise<void>}
 */
let updateComment = async(id, comment) => {
        let btnText = (comment) ? 'Изменить' : 'Добавить';
        const { value: text } = await Swal.fire({
        input: 'textarea',
        inputLabel: 'Комментарий',
        inputPlaceholder: 'Введите комментарий',
        inputValue: comment,
        showCancelButton: true,
        confirmButtonText: btnText,
        cancelButtonText: 'Назад',
    })

    if (text) {
        return window.livewire.emit('commentUpdate', {text, id})
    }
}

/**
 * Обновление закупочной цены
 *
 * @param id
 * @param price
 * @returns {Promise<void>}
 */
let priceUpdate = async (id, price) => {
    const { value: newPrice } = await Swal.fire({
        input: 'text',
        title: 'Цена зкупки',
        inputPlaceholder: 'Введите цену',
        inputValue: price,
    })

    if(newPrice) {
        return window.livewire.emit('editPrice', {id, newPrice})
    }
}

/**
 * Модалка для добавления даты частичной оплаты
 *
 * @param id
 * @param url
 * @returns {Promise<void>}
 */
let dateModal = async (id, url) => {
    const { value: newDate } = await Swal.fire({
        title: 'Введите дату',
        html: '<input type="date" class="form-control" name="date" id="swal-date">',
        preConfirm: () => {
            return document.getElementById('swal-date').value
        }
    })

    if(newDate) {
        let data = new FormData();
        data.append('newDate', newDate)
        data.append('id', id)

        let response = await fetch(url, {
            method: 'POST',
            body: data
        })
        let result = await response.json()
        if(result.id) {
            let div = document.createElement('div')
            div.setAttribute('style', 'margin-bottom: 5px; border-bottom: 1px grey solid')
            div.id = `date_${result.id}`
            div.innerHTML = new Date(newDate).toLocaleDateString() + ` <i style="cursor:pointer" class="fas fa-times text-danger cp"" onclick="dateModalDelete(\'${result.id}\', \'http://atrienergo/administration/module/detail_pay/delete_date/${result.id}\')"></i>`
            $('#date_pay').append(div)
        }
        Swal.fire(result.messages)
    }
}

/**
 * Модалка для добавления даты
 *
 * @param id
 * @param key - поле, которое будем обновлять
 * @returns {Promise<void>}
 */
let updateDate = async (id, key) => {
    let title = (key === 'pay_date') ? 'счета' : 'оплаты'
    const { value: newDate } = await Swal.fire({
        title: 'Дата ' + title,
        html: '<input type="date" class="form-control" name="date" id="swal-date">',
        preConfirm: () => {
            return document.getElementById('swal-date').value
        }
    })

    if(newDate) {
        return window.livewire.emit('updateDate', {date: newDate, id: id, key: key})
    }
}

/**
 * Модалка для удаления Даты частичной оплаты
 *
 * @param id
 * @param url
 * @returns {Promise<void>}
 */
let dateModalDelete = async (id, url) => {
    Swal.fire({
        title: 'Вы уверены?',
        text: 'Удалить дату',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Да, удалить!',
        cancelButtonText: 'Отменить',
    }).
    then(
        async (swalResult) => {
        let confirmed = swalResult.isConfirmed
        if (confirmed) {
            let data = new FormData()
            data.append('delete', confirmed)
            let response = await fetch(url, {
                method: 'POST',
                body: data,
            })

            let result = await response.json();
            if(result.result) {
                $(`#date_${result.id}`).remove()
            }
            Swal.fire(result.messages)
        }
    })
}

/**
 *
 * Обновление статуса заявки
 *
 * @param id
 * @param status
 * @returns {Promise<void>}
 */
let modalStatusUpdate = async (id, status) => {
    let data = {id, status}
    switch (status) {
        case 2:
            const { value: text } = await Swal.fire({
                title: 'Отправить на доработку?',
                input: 'textarea',
                inputLabel: 'Комментарий:',
                inputPlaceholder: 'Введите коментарий',
                inputAttributes: {
                    'aria-label': 'Тут ваш комментари'
                },
                showCancelButton: true,
                confirmButtonText: 'Продолжить',
                cancelButtonText: 'Закрыть',
            })
            if (text) {
                data.comment = text
                return window.livewire.emit('statusUpdate', data)
            }
        break
        case 3:
            Swal.fire({
                title: 'Согласовать заявку?',
                text: 'Вы хотите согласовать заявку?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да, согласовать',
                cancelButtonText: 'Назад',
            }).
            then(async (result) => {
                if (result.isConfirmed) {
                    data.comment = ''
                    return window.livewire.emit('statusUpdate', data)
                }
            })
    }
}

/**
 * Отмены оплаты на детальной странице
 *
 * @param id
 * @returns {Promise<void>}
 */
let cancelPayModal = async (id) => {
    await Swal.fire({
        title: "Вы уверены?",
        text: "Отменить оплату",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: 'Закрыть'
    })
        .then(async (result) => {
            if (result.isConfirmed) {
                let data = {
                    id: id,
                    data: {
                        pay: 0
                    },
                }

                return window.livewire.emit('cancelPay', data)
            }
        });
}

/**
 * Отмена оплаты по причине
 *
 * @param id
 * @returns {Promise<void>}
 */
let cancelPay = async (id) => {
    const { value: select } = await Swal.fire({
        title: 'Отмена оплаты',
        text: 'Выберите причину отмены',
        input: 'select',
        inputOptions: {
            0: 'Оплачено по акту сверки',
            1: 'Дублируется',
            2: 'Не актуально',
            3: 'Другое'
        },
        inputPlaceholder: 'Оплачено по акту сверки',
        showCancelButton: true,
        confirmButtonText: 'Выполнить',
        cancelButtonText: 'Отмена',
    })

    if(select) {
        let data = {
            data: {
                pay: 2,
                reason: select
            },
            id: id,
        }
        return window.livewire.emit('cancelPay', data)
    }
}

let deleteUserList = async (element, id, route) => {
    if(id) {
        await Swal.fire({
            title: "Вы уверены?",
            text: "Удалить запись!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: 'Закрыть'
        })
            .then(async (result) => {
                if (result.isConfirmed) {
                    $.post(route, {id: id}, response => {
                        if(response) {
                            let table = $('#god_table').dataTable()

                            table
                                .api()
                                .row()
                                .remove($(element).parents('tr'))
                                .draw();

                            Swal.fire({
                                icon: 'success',
                                title: 'Данные удалены!'
                            })
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Произошла ошибка!'
                            })
                        }

                  })
                }
            })
    }
}

/**
 * Вывод превью в модалке
 *
 * @param value
 * @returns {Promise<void>}
 */
let preview = async (value) => {
    if(value) {
        let reader = new FileReader()
        let img = $('#preview-image').get(0)
        let file = value.files[0]

        reader.onload = () => {
            img.src = reader.result
            img.style.width = '150px';
            img.style.height = '150px';
        }
        reader.readAsDataURL(file);
    }
}

let confirmWithoutLooking = async (route) => {
    await Swal.fire({
        title: 'Вы уверены что хотите подтвердить статус?',
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Да',
        cancelButtonText: `Нет`,
    }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            await $.post(route, {
                status: 3,
                comment: null
            }, (response) => {
                console.log(response)
            })
        }
    })
}


/**
 *
 * @param module
 * @param route
 */
let showDataTables = async (module, route) => {
    if(!module) return

    if(!route) return

    await $.post(route, {module: module}, response => {
        let jsonData = JSON.parse(response)
        if(jsonData) {
            let table = $('#god_table').dataTable()
            let data = Object.values(jsonData);
            table
                .api()
                .clear()
                .rows
                .add(data)
                .draw()
        }
    })

}

let confirmDelete = (id) => {
    Swal.fire({
        title: 'Подтвердить удаление?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Удалить!',
        cancelButtonText: 'Назад',
    }).
    then((result) => {
        if(result.isConfirmed) {
            window.livewire.emit('delete', id);
        }
    })
}