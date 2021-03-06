<?php  namespace Rossedman\Teamwork;

use Rossedman\Teamwork\Traits\TimeTrait;
use Rossedman\Teamwork\Traits\RestfulTrait;

class Task extends AbstractObject {

    use RestfulTrait, TimeTrait;

    protected $wrapper  = 'task';

    protected $endpoint = 'tasks';

    /**
     * Get All Tasks
     * GET /tasks.json
     *
     * @param null $args
     *
     * @return mixed
     */
    public function all($args = null)
    {
        $this->areArgumentsValid($args, ['filter', 'page', 'pageSize', 'startdate', 'enddate', 'updatedAfterDate', 'completedAfterDate', 'completedBeforeDate', 'showDeleted', 'includeCompletedTasks', 'includeCompletedSubtasks', 'creator-ids', 'include', 'responsible-party-ids', 'sort', 'getSubTasks', 'nestSubTasks', 'getFiles', 'dataSet', 'includeToday', 'ignore-start-date']);

        return $this->client->get($this->endpoint, $args)->response();
    }

    public function allV2($args = null)
    {
        $this->areArgumentsValid($args, ['filter', 'page', 'pageSize', 'startdate', 'enddate', 'updatedAfterDate', 'completedAfterDate', 'completedBeforeDate', 'showDeleted', 'includeCompletedTasks', 'includeCompletedSubtasks', 'creator-ids', 'include', 'responsible-party-ids', 'sort', 'getSubTasks', 'nestSubTasks', 'getFiles', 'dataSet', 'includeToday', 'ignore-start-date', 'includeCustomFields']);

        return $this->client->get('projects/api/v2/tasks', $args)->response();
    }

    /**
     * Complete A Task
     * PUT tasks/{id}/complete.json
     *
     * @return mixed
     */
    public function complete()
    {
        return $this->client->put("$this->endpoint/$this->id/complete", [])->response();
    }

    /**
     * Uncomplete A Task
     * PUT tasks/{id}/uncomplete.json
     *
     * @return mixed
     */
    public function uncomplete()
    {
        return $this->client->put("$this->endpoint/$this->id/uncomplete", [])->response();
    }

    /**
     * Time Totals
     * GET /projects/{id}/time/total.json
     *
     * @return mixed
     */
    public function timeTotal($args = null)
    {
        return $this->client->get("$this->endpoint/$this->id/time/total", $args)->response();
    }

    /**
     * Edit A Task
     * PUT tasks/{id}.json
     *
     * @return mixed
     */
    public function edit($args = [])
    {
        return $this->client->put("$this->endpoint/$this->id", ['todo-item' => $args])->response();
    }

    /**
     * Get A Task
     * GET tasks/{id}.json
     *
     * @return mixed
     */
    public function get($args = [])
    {
        return $this->client->get("$this->endpoint/$this->id", $args)->response();
    }

    /**
     * Add files to a task
     * POST tasks/{id}/files.json
     *
     * @return mixed
     */
    public function attachFiles($args)
    {
        return $this->client->put("$this->endpoint/$this->id/files", ['task' => $args])->response();
    }
}
