<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\ListOfValue\AboutUsResource;
use App\Http\Resources\Web\ListOfValue\AchievementSummaryCollection;
use App\Http\Resources\Web\ListOfValue\BankAccountCollection;
use App\Http\Resources\Web\ListOfValue\BannerCollection;
use App\Http\Resources\Web\ListOfValue\CareerCollection;
use App\Http\Resources\Web\ListOfValue\CompanyResource;
use App\Http\Resources\Web\ListOfValue\ContactUsResource;
use App\Http\Resources\Web\ListOfValue\MissionVisionCollection;
use App\Http\Resources\Web\ListOfValue\OurCoreValueResource;
use App\Http\Resources\Web\ListOfValue\OurProgramResource;
use App\Http\Resources\Web\ListOfValue\OurStoryResource;
use App\Http\Resources\Web\ListOfValue\PrivacyPolicyResource;
use App\Http\Resources\Web\ListOfValue\ProductionResource;
use App\Http\Resources\Web\ListOfValue\ReportDocumentCategoryCollection;
use App\Http\Resources\Web\ListOfValue\ReportDocumentCollection;
use App\Http\Resources\Web\ListOfValue\SocialMediaCollection;
use App\Http\Resources\Web\ListOfValue\UpcomingEventResource;
use App\Models\BankAccount;
use App\Models\Banner;
use App\Models\ListOfValue;
use App\Models\Page;
use App\Models\SocialMedia;
use Exception;

class ListOfValueController extends Controller
{
    protected string $achievementSummaryType;
    protected string $missionVisionType;
    protected string $careerType;
    protected string $reportDocumentCategoryType;
    protected string $reportDocumentType;

    public function __construct()
    {
        parent::__construct();
        $this->achievementSummaryType = config('dummy.module.achievement_summary.key');
        $this->missionVisionType = config('dummy.module.mission_vision.key');
        $this->careerType = config('dummy.module.career.key');
        $this->reportDocumentCategoryType = config('dummy.module.report_document_category.key');
        $this->reportDocumentType = config('dummy.module.report_document.key');
    }

    public function banner()
    {
        try {
            $data = Banner::query()
                ->where('status', $this->active)
                ->orderBy('ordering')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new BannerCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function achievementSummary()
    {
        try {
            $data = ListOfValue::query()
                ->where('type', $this->achievementSummaryType)
                ->where('status', $this->active)
                ->orderBy('sequence')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new AchievementSummaryCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function ourProgram()
    {
        try {
            $data = Page::query()
                ->where('page', 'our_program')
                ->where('status', $this->active)
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new OurProgramResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function upcomingEvent()
    {
        try {
            $data = Page::query()
                ->where('page', 'upcoming_event')
                ->where('status', $this->active)
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new UpcomingEventResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function privacyPolicy()
    {
        try {
            $data = Page::query()
                ->where('page', 'privacy_policy')
                ->where('status', $this->active)
                ->first();
            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }
            return $this->responseSuccess(new PrivacyPolicyResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }


    public function contactUs()
    {
        try {
            $data = Page::query()
                ->where('page', 'contact_us')
                ->where('status', $this->active)
                ->first();
            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }
            return $this->responseSuccess(new ContactUsResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }


    public function socialMedia()
    {
        try {
            $data = SocialMedia::query()
                ->where('status', $this->active)
                ->orderBy('ordering')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new SocialMediaCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }


    public function aboutUs()
    {
        try {
            $data = Page::query()
                ->where('page', 'about_us')
                ->where('status', $this->active)
                ->first();
            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }
            return $this->responseSuccess(new AboutUsResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function ourStory()
    {
        try {
            $data = Page::query()
                ->where('page', 'our_story')
                ->where('status', $this->active)
                ->first();
            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }
            return $this->responseSuccess(new OurStoryResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function missionVision()
    {
        try {
            $data = ListOfValue::query()
                ->where('type', $this->missionVisionType)
                ->where('status', $this->active)
                ->orderBy('sequence')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new MissionVisionCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function ourCoreValue()
    {
        try {
            $data = Page::query()
                ->where('page', 'our_core_value')
                ->where('status', $this->active)
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new OurCoreValueResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function career()
    {
        try {
            $data = ListOfValue::query()
                ->where('type', $this->careerType)
                ->where('status', $this->active)
                ->orderBy('sequence')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new CareerCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function reportDocumentCategory()
    {
        try {
            $data = ListOfValue::query()
                ->where('type', $this->reportDocumentCategoryType)
                ->where('status', $this->active)
                ->orderBy('sequence')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new ReportDocumentCategoryCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function reportDocument()
    {
        try {
            $data = ListOfValue::query()
                ->where('type', $this->reportDocumentType)
                ->where('status', $this->active)
                ->orderBy('sequence')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            $categoryIds = $data->pluck('add_on.category_id')->filter()->unique()->values()->all();
            $categories = ListOfValue::query()
                ->where('type', $this->reportDocumentCategoryType)
                ->whereIn('id', $categoryIds)
                ->get(['id', 'title'])
                ->keyBy('id');

            return $this->responseSuccess(new ReportDocumentCollection($data, $categories));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function production()
    {
        try {
            $data = Page::query()
                ->where('page', 'production')
                ->where('status', $this->active)
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new ProductionResource($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }

    public function company()
    {
        try {
            $data = ListOfValue::query()
                ->where('status', $this->active)
                ->where('type', 'company')
                ->first();
            if (!$data) {
                return response()->json(['message' => 'Data not found'], 200);
            }
            return $this->responseSuccess(new CompanyResource($data));
        } catch (Exception $e) {

            return $this->responseError();
        }
    }
    public function bankAccount()
    {
        try {
            $data = BankAccount::query()
                ->where('status', $this->active)
                ->orderBy('ordering')
                ->orderByDesc('id')
                ->get();

            if ($data->isEmpty()) {
                return response()->json(['message' => 'Data not found'], 200);
            }

            return $this->responseSuccess(new BankAccountCollection($data));
        } catch (Exception $e) {
            return $this->responseError();
        }
    }
}
