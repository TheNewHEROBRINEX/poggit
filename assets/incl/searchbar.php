<?php

use poggit\Meta;
use poggit\release\Release;
use poggit\utils\PocketMineApi;

?>
<div class="togglebar-wrapper">
    <div class="togglebar">
        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#search-nav"
                aria-controls="search-nav" aria-expanded="false" aria-label="Toggle Search and Sorting">
            <img onclick="$('html, body').animate({scrollTop: 0},500);" class="sidesearch-btn"
                 src="<?= Meta::root() ?>res/search-icon.png"/>
        </button>
    </div>
    <nav class="search-nav collapse navbar-default" role="navigation" id="search-nav">
        <div class="search-header">
            <div class="release-search">
                <div class="resptable-cell">
                    <input type="text" class="release-search-input" id="pluginSearch" placeholder="Search Releases"
                           size="20">
                </div>
                <select id="pluginSearchField">
                    <option value="p/" selected>Plugin</option>
                    <option value="plugins/by/">Author</option>
                </select>
                <div class="action resptable-cell" id="searchButton">Search</div>
            </div>
            <div class="release-list-buttons">
                <div onclick="window.location = '<?= Meta::root() ?>plugins/authors';"
                     class="action resptable-cell">List Authors
                </div>
                <div onclick="window.location = '<?= Meta::root() ?>plugins/categories';"
                     class="action resptable-cell">List Categories
                </div>
            </div>
            <div class="release-filter">
                <select id="category-list" class="release-filter-select">
                    <option value="0" <?= isset($this->preferCat) ? "" : "selected" ?>>All Categories</option>
                    <?php
                    foreach(Release::$CATEGORIES as $catId => $catName) { ?>
                        <option <?= isset($this->preferCat) && $this->preferCat === $catId ? "selected" : "" ?>
                            value="<?= $catId ?>"><?= $catName ?></option>
                    <?php }
                    ?>
                </select>
            </div>
            <div class="release-filter">
                <select id="api-list" class="release-filter-select">
                    <option value="All API Versions" selected>All API Versions</option>
                    <?php
                    foreach(array_reverse(PocketMineApi::$VERSIONS) as $apiversion => $description) { ?>
                        <option value="<?= $apiversion ?>"><?= $apiversion ?></option>
                    <?php }
                    ?>
                </select>
            </div>
            <div class="release-filter action" id="release-sort-button">Sort</div>
        </div>
        <div style="display: none;" id="release-sort-dialog" title="Sort releases">
            <ol id="release-sort-list">
                <li class="release-sort-row release-sort-row-template">
                    <select class="release-sort-category">
                        <option value="popularity">Popularity</option>
                        <option value="state-change-date">Date featured/approved/voted</option>
                        <option value="submit-date">Date submitted (latest version)</option>
                        <!--                <option value="submit-date-first">Date submitted (first version)</option>-->
                        <option value="state">Featured > Approved > Voted</option>
                        <option value="total-downloads">Downloads (total)</option>
                        <option value="downloads">Downloads (latest version)</option>
                        <option value="mean-review">Average review score (latest version)</option>
                        <option value="name">Plugin name</option>
                    </select>
                    <select class="release-sort-direction">
                        <option value="asc">Ascending</option>
                        <option value="desc" selected>Descending</option>
                    </select>
                    <span class="action release-sort-row-close">&cross;</span>
                </li>
            </ol>
            <span class="action" id="release-sort-row-add">+</span>
        </div>
    </nav>
</div>
