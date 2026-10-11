<?php if ($comments === []): ?>
    <p class="text-muted" id="comments-empty">Комментариев пока нет</p>
<?php else: ?>
    <?php foreach ($comments as $comment): ?>
        <article class="comment-card"
                 id="comment-<?= (int) $comment['id'] ?>">
            <div class="d-flex justify-content-between flex-wrap">
                <strong>
                    <?= esc($comment['name']) ?>
                </strong>

                <span class="comment-meta">
                    <?= esc($comment['date']) ?>
                    · ID <?= (int) $comment['id'] ?>
                </span>
            </div>

            <p class="mt-3 mb-3"><?= nl2br(esc($comment['text'])) ?></p>

            <button type="button"
                    class="btn btn-outline-danger btn-sm delete-comment"
                    data-id="<?= (int) $comment['id'] ?>">
                Удалить
            </button>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
