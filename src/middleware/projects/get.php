<?php
if (isset($viewData)) {
    header("viewdata: {$viewData['test']}");
} else {
    header("viewdata: null");
}