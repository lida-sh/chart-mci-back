<?php

namespace App\Http\Controllers\V1;

use App\CenterPivot;
use App\Http\Controllers\Controller;
use App\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\Admin\ApiController;
use App\OutsourcingActivity;

class OutsourcingController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function calculate(Request $request)
    {

        $provinceId = $request->province_id;
        $calculatMethod = $request->calculation_method;
        $centerPivotId = $request->center_pivot_id;
        $province = Province::findOrFail($provinceId);
        $regionCoefficient  = $province->regionTechnicalInfo->region_coefficient;
        $activities = [];
        foreach (OutsourcingActivity::all() as $outsourcingActivity) {
            $activities[$outsourcingActivity->id] = [
                'id' => $outsourcingActivity->id,
                'title' => $outsourcingActivity->activity_title,
                'unit' => $outsourcingActivity->unit,
                'manMonths' => 0,
                'count' => 0,
            ];
        }

        if ($calculatMethod == 1) {
            $centerPivot = CenterPivot::findOrFail($centerPivotId);
            [$activities[1]['manMonths'], $activities[1]['count']] = $this->copperNetworkMaintenance($centerPivot);
            [$activities[2]['manMonths'], $activities[2]['count']] = $this->fiberNetworkMaintenance($province, $centerPivot);
            [$activities[3]['manMonths'], $activities[3]['count']] = $this->FTTXMaintenance($centerPivot);
            [$activities[4]['manMonths'], $activities[4]['count']] = $this->accessSistemsMaintenance($centerPivot);
            [$activities[5]['manMonths'], $activities[5]['count']] = $this->lowCapacityMaintenance($centerPivot);
        } else {
            $provisionedLinesCount = $province->regionTechnicalInfo->provisioned_lines_count;
            $provisionedLinesmanMonths = 0;
            $towerCount = $province->regionTechnicalInfo->tower_count;
            $towerCountmanMonths = 0;
            foreach ($province->centerPivots as $center) {
                [$manMonths, $count] = $this->copperNetworkMaintenance($center);
                $activities[1]['manMonths'] += $manMonths;
                $activities[1]['count'] += $count;
                [$manMonths, $count] = $this->fiberNetworkMaintenance($province, $center);
                $activities[2]['manMonths'] += $manMonths;
                $activities[2]['count'] += $count;

                [$manMonths, $count] = $this->FTTXMaintenance($center);
                $activities[3]['manMonths'] += $manMonths;
                $activities[3]['count'] += $count;

                [$manMonths, $count] = $this->accessSistemsMaintenance($center);
                $activities[4]['manMonths'] += $manMonths;
                $activities[4]['count'] += $count;

                [$manMonths, $count] = $this->lowCapacityMaintenance($center);
                $activities[5]['manMonths'] += $manMonths;
                $activities[5]['count'] += $count;
            }
            foreach ($province->powerSupplies as $powerSupply) {
                $activities[6]['manMonths'] += $powerSupply->unit * $powerSupply->pivot->power_supply_count;
                $activities[6]['count'] += $powerSupply->pivot->power_supply_count;
            }
            foreach ($province->facilities as $facility) {
                $activities[7]['manMonths'] += $facility->unit * $facility->pivot->facility_count;
                $activities[7]['count'] += $facility->pivot->facility_count;
            }
            if ($provisionedLinesCount > 0 && $provisionedLinesCount <= 80000) {
                $provisionedLinesmanMonths = 1;
            } else if ($provisionedLinesCount > 80000) {
                $provisionedLinesmanMonths = 1 + 0.1 * (($provisionedLinesCount - 80000) / 12000);
            }
            $activities[8]['manMonths'] = $provisionedLinesmanMonths;
            $activities[8]['count'] = $provisionedLinesCount;
            if ($towerCount > 0 && $towerCount < 60) {
                $towerCountmanMonths = 1;
            } else if ($towerCount > 60) {
                $towerCountmanMonths = 1 + 0.1 * (($towerCount - 60) / 6);
            }
            $activities[9]['manMonths'] = $towerCountmanMonths;
            $activities[9]['count'] = $towerCount;
        }
        return $this->successResponse([
            "activities" => $activities,
            "regionCoefficient" => $regionCoefficient
        ], 200);
    }
    protected function lowCapacityMaintenance(CenterPivot $centerPivot)
    {
        $countCentersUnit = 0;
        $count = $centerPivot->telecomCenters()
            ->where('capacity', 0)
            ->count();
        if ($count <= 20) {
            $countCentersUnit = 1;
        } else if ($count > 20) {
            $countCentersUnit = 1 + 0.1 * (($count - 20) / 2);
        }
        return [$countCentersUnit, $count];
    }
    protected function accessSistemsMaintenance(CenterPivot $centerPivot)
    {
        $accessCount = $centerPivot->active_access_count;
        $accessCountUnit = 0;
        if ($accessCount < 1000) {
            $accessCountUnit = 0;
        } else if ($accessCount >= 1000 && $accessCount <= 5000) {
            $accessCountUnit = 1;
        } else if ($accessCount > 5000) {
            $accessCountUnit = 1 + 0.1 * (($accessCount - 5000) / 1000);
        }
        return [$accessCountUnit, $accessCount];
    }
    protected function FTTXMaintenance(CenterPivot $centerPivot)
    {
        $fttxCount = $centerPivot->active_fttx_count;
        if ($fttxCount <= 1000) {
            $fttxUnit = 1;
        } else {
            $fttxUnit = 1 + 0.1 * (($fttxCount - 1000) / 400);
        }
        return [$fttxUnit, $fttxCount];
    }
    protected function fiberNetworkMaintenance(Province $province, CenterPivot $centerPivot)
    {
        $SumOfTransFibers = 0;
        $sumOfAccessFibers = 0;
        $RT = 0;
        $RA = 0;
        foreach ($province->centerPivots as $center) {
            $SumOfTransFibers += $center->transport_fiber_length_km;
            $sumOfAccessFibers += $center->access_fiber_length_km;
        }
        
        $transFiber = $centerPivot->transport_fiber_length_km ?? 0;
        $accessFiber = $centerPivot->access_fiber_length_km ?? 0;
        $transUnit = 0;
        $accessUnit = 0;
        if ($SumOfTransFibers > 0) {
            if ($SumOfTransFibers <= 250) {
                $RT = 1;
            } else if ($SumOfTransFibers > 250 && $SumOfTransFibers <= 1000) {
                $RT = 1 + (0.1 * (($SumOfTransFibers - 250) / 50));
            } else if ($SumOfTransFibers > 1000) {
                $RT = 1 + (0.1 * (((1000 - 250) / 50) + (($SumOfTransFibers - 1000) / 60)));
            }
            $transUnit = ($RT * $transFiber) / $SumOfTransFibers;
            if($sumOfAccessFibers > 0){
                if ($sumOfAccessFibers <= 50) {
                    $RA = 1;
                } else if ($sumOfAccessFibers > 50) {
                    $RA = 1 + (0.1 * (($sumOfAccessFibers - 50) / 20));
                }
            }
            
        }
        $accessUnit = $RA * ($accessFiber / $sumOfAccessFibers);
        return [$transUnit + $accessUnit, $transFiber + $accessFiber];
    }
    protected function copperNetworkMaintenance(CenterPivot $centerPivot)
    {

        $installedDivide = 130;
        $faultDivide = 210;
        $active_copper = 0;
        $MDFcount = 0;
        $w = 0;
        $x = 0;
        foreach ($centerPivot->telecomCenters as $telecomCenter) {
            if ($telecomCenter->capacity == 1) {
                $MDFcount++;
                $active_copper += $telecomCenter->in_service_count;
            }
        }
        $allInService = $active_copper + $centerPivot->active_access_count;
        if ($allInService > 0 && $allInService < 20000) {
            $x = 1;
        } else if ($allInService > 20000 && $allInService < 50000) {
            $x = 3;
        } else if ($allInService > 50000) {
            $x = 5;
        }
        if ($MDFcount >= 1) {
            $w = 1 + ($MDFcount - 1) * 0.1;
        }
        $y = ($centerPivot->installed_and_displacement_count / 12) / $installedDivide;
        $z = ($centerPivot->fault_count / 12) / $faultDivide;
        $unit = $x + $w * ($y + $z);
        return [$unit, $allInService];
    }
}
