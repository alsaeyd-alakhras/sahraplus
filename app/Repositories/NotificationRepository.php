<?php
namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
	 /**
     * @var Notification
     */
    protected Notification $notification;

    /**
     * Notification constructor.
     *
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get all notification.
     *
     * @return Notification $notification
     */
    public function all()
    {
        return $this->notification->get();
    }

     /**
     * Get notification by id
     *
     * @param $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->notification->find($id);
    }
    public function getQuery()
    {
        return $this->notification->query();
    }

    /**
     * Delete Notification
     *
     * @param $data
     * @return Notification
     */
   	 public function delete(int $id)
    {
        $notification = $this->notification->find($id);
        $notification->delete();
        return $notification;
    }
}
