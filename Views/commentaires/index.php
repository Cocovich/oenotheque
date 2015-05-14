<?php

if (! isset($body))
    $body = "";
if (isset($_SESSION["user_id"])) {
    $form = <<<HTML
        <div class="col-md-push-3 col-md-3" style="margin-top: 15px;" id="div-filters">
            <form action="" method="post" id="form-filters" name="form-filters" class="form-inline">
                <label for="selectLimit" class="control-label">Affichage&nbsp;</label>
                <select name="limit" id="selectLimit" class="form-control" onchange="this.form.submit()">
                    $limits
                </select>
            </form>
        </div>
HTML;
}
else{
    $form="";
}
$body .= <<<HTML

<div id="comments">

    <div class="row" id="comments-header">
        <h3 class="col-md-6">Derniers commentaires</h3>
        $form
    </div>

HTML;


$body .= "<div>";
$body .= "<ul class='list-group' id='list-comments'>";
foreach ($comments as $num => $comment) {
    $link = (isset($_SESSION["user_id"])) ? "<a href='#' class='remove-comment' data-id='{$comment->getId()}'><span class='glyphicon glyphicon-remove'></span></a>" : "";
    $body .= <<<HTML
        <li class="list-group-item" data-id="{$comment->getId()}">
            <div class="comment-container">
                <p>
                    <span class="comment-username">{$comment->getPseudo()}</span>&nbsp;
                    (<span class="comment-date">le {$comment->getDateAjout()->format("d/m à H:i")}</span>)
                    {$link}
                </p>

                <div class="comment-content">

                    <p class="comment-text">
                        {$comment->getContenu()}
                    </p>
                    <p class="comment-wine-name">
                        <a href="?action=show&id={$comment->getVin()->getId()}">{$comment->getVin()->getNom()}</a>
                    </p>
                </div>
            </div>
        </li>
HTML;
}
$body .= "</ul>";
$body .= "</div>";

$body .= "</div>";

require_once "Views/commentaires/modal-confirmation.php";
require_once "Views/commentaires/modal-response.php";


$body .= "<script>";
$body .= <<<JS
    $("a.remove-comment").click(function() {
        var id = $(this).data("id");
        var li = $(this).closest('li')

        $("#delete-comment").modal('show');

        $("#confirm-delete").click(function() {
            $("#delete-comment").modal('hide');
            {
                $.ajax({
                    type: "POST",
                    url: "index.php?action=remove_comment",
                    data: {
                        id : id
                    },
                    dataType: "json",
                    success : function(response) {
                        $("#deleted-comment .modal-title").html(response.title);
                        $("#deleted-comment .modal-body").html(response.message);
                        $("#deleted-comment").modal('show');

                        if (response.title == "Succès") {
                            $('#deleted-comment').on('hidden.bs.modal', function (e) {
                                location.reload();
                            });
                            li.hide();
                        }

                    }
                });
            }
        });
    });
JS;
$body .= "</script>";


require_once("Views/layout.php");
