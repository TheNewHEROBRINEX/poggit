<?php

/*
 * Poggit
 *
 * Copyright (C) 2016-2017 Poggit
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

namespace poggit\release\submit;

use poggit\Meta;
use poggit\module\AjaxModule;
use poggit\release\SubmitException;
use poggit\utils\lang\Lang;

class NewSubmitAjax extends AjaxModule {
    public function getName(): string {
        return "submit.new.ajax";
    }

    protected function impl() {
        header("Content-Type: application/json");
        $json = Meta::getInput();
        $data = json_decode($json);
        $form = $data->form;
        $action = $data->action;
        $token = $data->submitFormToken;
        if(!isset($_SESSION["poggit"]["submitFormToken"][$token])) $this->errorBadRequest("Wrong SFT! Did you click the submit button twice?");
        $args = $_SESSION["poggit"]["submitFormToken"][$token];

        $submission = new PluginSubmission;
        Lang::copyToObject($form, $submission); // do this before other assignments to prevent overriding
        $submission->action = $action;
        Lang::copyToObject($args, $submission);
        if($submission->mode !== "submit") $submission->name = $submission->refRelease->name;
        if($submission->mode === "edit") {
            $submission->version = $submission->refRelease->version;
            $submission->spoons = $submission->spoons ?: $submission->refRelease->spoons;
        } else {
            $submission->outdated = false;
        }
        if($submission->lastValidVersion === false) $submission->changelog = false;
        if(Meta::getAdmlv() < Meta::ADMLV_REVIEWER) $submission->official = false;

        try {
            $submission->validate();
            $submission->resourcify();
            $submission->processArtifact();
            $submission->save();
            unset($_SESSION["poggit"]["submitFormToken"][$token]);

            echo json_encode(["status" => true, "link" => Meta::root() . "p/{$submission->name}/{$submission->version}"]);
        } catch(SubmitException $e) {
            $this->errorBadRequest($e->getMessage());
        }
    }

    public function errorBadRequest(string $message, bool $escape = true) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "error" => $message,
            "input" => json_decode(Meta::getInput()),
        ]);
        die;
    }
}
