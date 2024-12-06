<?php

namespace App\Http\Controllers\Tag;

use App\Http\Controllers\Base\BaseController;
use App\Models\Tag\TagList;

class TagController extends BaseController
{
    public function getTagList($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $tagList = TagList::where('tag_int_status', 1)->get();

                if ($tagList->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No tag found', code: 404);
                }

                return $this->sendResponse(message: 'Get Tag List', result: $tagList);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }
}
