<?php

/*
 * Copyright 2016 poggit
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace poggit\page\build;

use poggit\model\BuildThumbnail;
use poggit\Poggit;

class RecentBuildPageVariant extends BuildPageVariant {
    /** @var string|null */
    private $error = null;

    public function __construct(BuildPage $page, string $error = null) {
        $this->error = $error;
    }

    public function getTitle() : string {
        return $this->error === null ? "Recent Builds" : "Builds Not Found";
    }

    public function output() {
        if($this->error !== null) {
            echo "<div id='recentBuildsError'>";
            echo $this->error;
            echo "</div>";
        }
        /** @var BuildThumbnail[] $recent */
        $recent = [];
        foreach(Poggit::queryAndFetch("SELECT b.buildId AS bidg, b.internal AS bidi,
                p.name AS pname, r.owner AS uname, r.name AS rname, unix_timestamp(b.created) AS created
                FROM builds b INNER JOIN projects p ON b.projectId=p.projectId INNER JOIN repos r ON p.repoId=r.repoId
                WHERE class = 1 ORDER BY created DESC LIMIT 20") as $row) {
            $build = new BuildThumbnail();
            $build->globalId = (int) $row["bidg"];
            $build->internalId = (int) $row["bidi"];
            $build->projectName = $row["pname"];
            $build->repoName = $row["rname"];
            $build->repoOwnerName = $row["uname"];
            $build->created = (int) $row["created"];
            $recent[] = $build;
        }
        ?>
        <div id="recentBuilds">
            <?php if($this->error !== null) { ?>
                <p>Here are some recent builds from other users:</p>
            <?php } else { ?>
                <h2>Recent builds</h2>
            <?php } ?>
            <!-- TODO add recent build list -->
            <?php foreach($recent as $build) { ?>
                <div class="buildVar">
                    <h3><?= htmlspecialchars($build->projectName ) ?></h3>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    public function getError() {
        return $this->error;
    }

    public function setError($error) : RecentBuildPageVariant {
        $this->error = $error;
        return $this;
    }
}