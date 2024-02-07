<div class="row">
    <form method="get">
        <input type="search" name="query" value="<?= h($query)?>"/>
<!--        <input type="hidden" name="page" value="--><?php //=$page?><!--" />-->
<!--        <input type="hidden" name="perPage" value="--><?php //=$perPage?><!--" />-->
        <input type="submit" />
    </form>
</div>

<div class="row">
    <div class="col-6">
        <table>
            <tbody>
            <?php if(count($results) > 0):?>
                <?php foreach ($results as $result): ?>
<!--                --><?// pr($result) ?>
                    <tr class="search-result">
                        <td><?= $result['search_id'] ?></td>
                        <td><?= $result['search_word'] ?></td>
                        <td><?= $result['product_name'] ?></td>
                        <td><?= $result['product_id'] ?></td>
                        <td><?= $result['brand_name'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No record found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        <? if(count($results)): ?>
            <!-- Pagination links -->
            <ul class="pagination">
                <?= $this->Paginator->prev("<<") ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(">>") ?>
            </ul>
        <? endif ?>
    </div>

</div>
