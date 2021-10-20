<?php

namespace App\Helpers;

class Helper
{
    /**
     * Get pagination summary based on given inputs
     * @param int $total_item Total item e.g. 100
     * @param int $item_per_page Item per page e.g. 10
     * @param int $current_page Current page e.g. 1
     * @return string e.g. Showing 1 to 10 of 100 records
     */
    public static function paginationSummary(int $total_item, int $item_per_page, int $current_page): string
    {
        $from = $total_item > 0 ? 1 : 0;
        $to = $total_item;

        if ($total_item > $item_per_page) {
            if ($current_page === 1) {
                $from = 1;
                $to = $item_per_page;
            } else {
                if (($total_item - ($current_page * $item_per_page)) > $item_per_page) {
                    $from = (($current_page - 1) * $item_per_page) + 1;
                    $to = $current_page * $item_per_page;
                } else {
                    $from = (($current_page - 1) * $item_per_page) + 1;
                    $to = min(($current_page * $item_per_page), $total_item);
                }
            }
        }

        return "Showing {$from} to {$to} of {$total_item} records";
    }

    /**
     * Get bootstrap alert box
     * @param string $message Message e.g. Operation Succeed!
     * @param string $type Type e.g. success [available type: success, warning, error, info]
     * @param bool $close_button Close button e.g. true/false
     * @return string HTML markup
     */
    public static function message(string $message, string $type = "success", bool $close_button = true): string
    {
        if ($type === "success") {
            $class = "success";
            $icon = "check-circle";
        } elseif ($type === "warning") {
            $class = "warning";
            $icon = "error";
        } elseif ($type === "error") {
            $class = "danger";
            $icon = "error";
        } elseif ($type === "info") {
            $class = "info";
            $icon = "info-circle";
        } else {
            $class = "success";
            $icon = "check-circle";
        }

        if ($close_button === true) {
            $close_button_html = "
                <button type='button' class='close'  data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            ";
        } else {
            $close_button_html = "";
        }

        $html = "
            <div class='alert alert-$class alert-dismissible'>
                $message
                $close_button_html
            </div>
        ";

        return $html;
    }
}
