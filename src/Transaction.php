<?php


namespace TradeSafe\Api;


use http\Exception;

class Transaction
{
    protected $data;

    /**
     * Create a transaction.
     *
     * @param $title
     * @param $description
     */
    public function __construct($title, $description)
    {
        $this->data = [];

        $this->set('title', $title);
        $this->set('description', $description);
        $this->set('industry', 'GENERAL_GOODS_SERVICES');
        $this->set('feeAllocation', 'SELLER');

        $this->set('currency', 'ZAR');
        $this->set('privacy', 'NONE');
    }

    public function init($data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }

    /**
     * Set transaction industry.
     *
     * @param $value
     * @throws \Exception
     */
    public function setIndustry($value)
    {
        switch ($value) {
            case "AGRICULTURE_LIVESTOCK_GAME":
            case "ART_ANTIQUES_COLLECTIBLES":
            case "VEHICLES_WATERCRAFT":
            case "CELLPHONES_COMPUTERS":
            case "CONSTRUCTION":
            case "FUEL":
            case "EVENTS":
            case "FILMS_PRODUCTION":
            case "CONTRACT_WORK_FREELANCING":
            case "GENERAL_GOODS_SERVICES":
            case "MERGERS_ACQUISITIONS":
            case "MINING":
            case "PROPERTY":
            case "RENEWABLES":
            case "RENTAL":
            case "SOFTWARE_DEV_WEB_DOMAINS":
                $this->set('industry', $value);
                break;
            default:
                throw new \Exception('Invalid industry');
        }
    }

    /**
     * Set transaction fee allocation.
     *
     * @param $value
     * @throws \Exception
     */
    public function setFeeAllocation($value)
    {
        switch ($value) {
            case "AGENT":
            case "BUYER":
            case "SELLER":
            case "BUYER_SELLER":
            case "BUYER_AGENT":
            case "SELLER_AGENT":
            case "BUYER_SELLER_AGENT":
                $this->set('feeAllocation', $value);
                break;
            default:
                throw new \Exception('Invalid fee allocation');
        }
    }

    /**
     * Set transaction privacy.
     *
     * @param $value
     * @throws \Exception
     */
    public function setPrivacy($value)
    {
        switch ($value) {
            case "NONE":
            case "ALL":
            case "DETAILS":
            case "CALCULATIONS":
                $this->set('privacy', $value);
                break;
            default:
                throw new \Exception('Invalid privacy setting');
        }
    }

    /**
     * Add address.
     *
     * @param $addressId
     * @param $deliveryInstructions
     */
    public function addAddress($addressId, $deliveryInstructions)
    {
        $this->set('addressId', $addressId);
        $this->set('deliveryInstructions', $deliveryInstructions);
    }

    /**
     * Add Party.
     *
     * @param $role
     * @param null $token
     * @param null $email
     * @param null $fee
     * @param null $feeType
     * @param null $feeAllocation
     * @throws \Exception
     */
    public function addParty($role, $token = null, $email = null, $fee = null, $feeType = null, $feeAllocation = null)
    {
        $party = [];

        if (is_null($token) && is_null($email)) {
            throw new \Exception('A token or email address is required for a party');
        }

        if (isset($token)) {
            $party['token'] = $token;
        } elseif (isset($email)) {
            $party['email'] = $email;
        }

        switch ($role) {
            case "BUYER":
            case "SELLER":
                $party['role'] = $role;
                break;
            case "AGENT":
                $party['role'] = $role;

                if (is_null($fee) || is_null($feeType) || is_null($feeAllocation)) {
                    throw new \Exception('Party (' . $role . ') fee is invalid');
                }

                $party['fee'] = $fee;
                $party['feeType'] = $feeType;
                $party['feeAllocation'] = $feeAllocation;
                break;
            default:
                throw new \Exception('Invalid role: ' . $role);
        }

        $parties = $this->get('parties') ?: [];
        $parties[] = $party;

        $this->set('parties', $parties);
    }

    /**
     * Add Allocation.
     *
     * @param string $title
     * @param string $description
     * @param float $daysToDeliver
     * @param float $daysToInspect
     * @param float|null $value
     * @param float|null $units
     * @param float|null $unitCost
     * @throws \Exception
     */
    public function addAllocation(string $title, string $description, float $daysToDeliver, float $daysToInspect, float $value = null, float $units = null, float $unitCost = null)
    {
        if (is_null($value) && (is_null($units) || is_null($unitCost))) {
            throw new \Exception('Allocation missing value or units');
        }

        $allocation = [];

        $allocation['title'] = $title;
        $allocation['description'] = $description;
        $allocation['daysToDeliver'] = $daysToDeliver;
        $allocation['daysToInspect'] = $daysToInspect;

        if (isset($value)) {
            $allocation['value'] = $value;
        }

        if (isset($units) && isset($unitCost)) {
            $allocation['value'] = $units * $unitCost;
            $allocation['units'] = $units;
            $allocation['unitCost'] = $unitCost;
        }

        $allocations = $this->get('allocations') ?: [];
        $allocations[] = $allocation;

        $this->set('allocations', $allocations);
    }

    /**
     * Get field.
     *
     * @param $field
     * @return mixed
     */
    public function get($field)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Set field.
     *
     * @param $field
     * @param $value
     */
    public function set($field, $value): void
    {
        $this->data[$field] = $value;
    }
}
