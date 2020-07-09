<?php

namespace nodespark\DESConnector\Elasticsearch\Aggregations\Bucket;

use nodespark\DESConnector\Elasticsearch\Aggregations\AggregationInterface;

/**
 * Class Filter
 *
 * @package nodespark\DESConnector\Elasticsearch\Aggregations\Bucket
 */
class Filter extends Bucket
{
    const TYPE = 'terms';

    protected $size;
    protected $order;
    protected $search = array();
    protected $filters = array();

    public function setSize($size)
    {
        $this->size = $size;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setSearch($search)
    {
        $this->search = $search;
    }

    public function addFilters($filters)
    {
        $this->filters[] = $filters;
    }

    public function constructAggregation()
    {
        $aggregation = parent::constructAggregation();

        // Set the additional parameters if needed.
        if (isset($this->size)) {
            $aggregation[$this->name][static::TYPE]['size'] = $this->size;
        }

        $aggregation[$this->name . '_global']['aggs'][$this->name]['aggs'][$this->name] = $aggregation[$this->name];

        $aggregation[$this->name . '_global']['aggs'][$this->name]['filter']['bool'] = array();
        if (!empty($this->search)) {
          $aggregation[$this->name . '_global']['aggs'][$this->name]['filter']['bool'] = $this->search['bool'];
        }

        if (!empty($this->filters)) {
          foreach ($this->filters as $filter) {
            if (!empty($filter['bool'])) {
              $aggregation[$this->name . '_global']['aggs'][$this->name]['filter']['bool'] += $filter['bool'];
            }
            elseif (!empty($filter[0]['bool'])) {
              $aggregation[$this->name . '_global']['aggs'][$this->name]['filter']['bool'] += $filter[0]['bool'];
            }
          }
        }

        if (!empty($aggregation[$this->name . '_global']['aggs'][$this->name]['filter']['bool'])) {
          $aggregation[$this->name . '_global']['global'] = (Object)array();
        }
        else {
          unset($aggregation[$this->name . '_global']['aggs'][$this->name]['filter']);
        }

        return $aggregation;
    }
}
