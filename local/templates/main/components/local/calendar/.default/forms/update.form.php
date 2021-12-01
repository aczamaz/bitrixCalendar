<div class="modal fade" id="update-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Редактировать задачу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-form-items">
                    <input type="hidden" name="date" value="">
                    <div class="form-group">
                        <label for="form-name">Имя задачи</label>
                        <input type="text" name="name" required class="form-control" id="form-name" placeholder="Имя задачи">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Статус задачи</label>
                        <select required class="form-control" name="status" id="exampleFormControlSelect1">
                            <option value="1">Пойду</option>
                            <option value="2">Под Вопросом</option>
                            <option value="3">Не пойду</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Описание задачи</label>
                        <textarea required class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="js-update-task" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>