<div id="ufModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="value">Value (<?php echo $indicator->measurement; ?>):</label>
                    <input type="number" class="form-control" name="value" id="value" placeholder="" required>
                </div>

                <div class="form-group">
                    <label for="date">Date: </label>
                    <input class="form-control" type="date" name="date" id="date" min="1900-01-01" required>
                </div>
            </div>
            <div class=" modal-footer">
                <input type="hidden" id="id" value="0">
                <button type="button" class="btn btn-success btn-sm" id="saveUf">Save</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>