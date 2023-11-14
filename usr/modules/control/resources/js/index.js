function confirmWithoutLooking(id){
    Swal.fire({
        title: 'Вы уверены что хотите подтвердить не глядя?',
        showCancelButton: true,
        confirmButtonText: 'Отправить',
        cancelButtonText: `Отмена`,
      }).then(async (result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          const lw = new LW;
          lw.on();
          await end(id, 3);
          await $grid.trigger('reloadGrid');
          lw.off();
        }
    })
}