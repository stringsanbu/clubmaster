<?php

namespace Club\BookingBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Club\UserBundle\Entity\Location;
use Club\BookingBundle\Entity\Field;
use Club\BookingBundle\Entity\Plan;
use Club\BookingBundle\Entity\PlanRepeat;

/**
 * PlanRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlanRepository extends EntityRepository
{
    public function getActive()
    {
        $past = new \DateTime();
        $past->modify('-7 day');

        return $this->createQueryBuilder('p')
            ->join('p.plan_repeats', 'pr')
            ->where('p.updated_at > :past')
            ->andWhere('p.status = :active')
            ->orWhere('p.end > :now')
            ->orWhere('p.repeating = true AND pr.ends_type = :never')
            ->orWhere('p.repeating = true AND pr.ends_type = :after')
            ->orWhere('(p.repeating = true AND pr.ends_type = :on AND pr.ends_on > :now)')
            ->setParameter('active', Plan::STATUS_ACTIVE)
            ->setParameter('past', $past)
            ->setParameter('now', new \DateTime())
            ->setParameter('never', 'never')
            ->setParameter('after', 'after')
            ->setParameter('on', 'on')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getQuery(\DateTime $date)
    {
        $ends_on = clone $date;
        $ends_on->modify('-1 day');

        $qb = $this->createQueryBuilder('p')
            ->select('p,pr')
            ->join('p.fields', 'f')
            ->join('p.plan_repeats', 'pr')
            ->where('p.status = :active')
            ->andWhere('(p.repeating = false) OR (pr.ends_type <> :ends_type) OR (pr.ends_type = :ends_type AND pr.ends_on > :ends_on)')
            ->setParameter('active', Plan::STATUS_ACTIVE)
            ->setParameter('ends_type', 'on')
            ->setParameter('ends_on', $ends_on);

        return $qb;
    }

    public function getICSByLocation(Location $location, \DateTime $date)
    {
        $plans = $this->getQuery($date)
            ->andWhere('f.location = :location')
            ->setParameter('location', $location->getId())
            ->getQuery()
            ->getResult();

        return $this->getIcsFromPlans($plans);
    }

    public function getICSByField(Field $field, \DateTime $date)
    {
        $plans = $this->getQuery($date)
            ->andWhere('f.id = :field')
            ->setParameter('field', $field->getId())
            ->getQuery()
            ->getResult();

        return $this->getIcsFromPlans($plans);
    }

    public function getIcsFromPlans($plans, Plan $plan=null)
    {
        if ($plan) {
            $plans = array($plan);
        }

        $vcalendar = new \Sabre\VObject\Component\VCalendar();

        foreach ($plans as $plan) {
            $this->addEvent($plan, $vcalendar);
        }

        print_r($vcalendar->serialize());die();
        return $vcalendar->serialize();
    }

    public function getBetweenByField(Field $field, \DateTime $start, \DateTime $end)
    {
        $ics = $this->getICSByField($field, $start);

        return $this->getPlansFromIcs($ics, $start, $end);
    }

    public function getPlansFromIcs($ics, \DateTime $start, \DateTime $end)
    {
        $calendar = \Sabre\VObject\Reader::read($ics);
        $calendar->expand($start, $end);

        $plans = array();
        if (count($calendar->VEVENT)) {
            foreach ($calendar->VEVENT as $event) {

                $eventStart = $event->DTSTART->getDateTime();
                $eventEnd = $event->DTEND->getDateTime();

                switch (true) {
                case ($eventEnd < $start):
                case ($eventStart > $end):
                    break;
                default:
                    preg_match("/^(\d+)_/", $event->UID, $o);
                    $plan_id = $o[1];
                    $plan = $this->_em->find('ClubBookingBundle:Plan', $plan_id);

                    $s = $plan->getStart();
                    $s->setDate(
                        $eventStart->format('Y'),
                        $eventStart->format('m'),
                        $eventStart->format('d')
                    );
                    $e = $plan->getEnd();
                    $e->setDate(
                        $eventEnd->format('Y'),
                        $eventEnd->format('m'),
                        $eventEnd->format('d')
                    );

                    $plan->setStart($s);
                    $plan->setEnd($e);

                    $plans[] = $plan;
                    break;
                }
            }
        }

        return $plans;
    }

    public function getBetweenByLocation(Location $location, \DateTime $start, \DateTime $end)
    {
        $ics = $this->getICSByLocation($location, $start);

        return $this->getPlansFromIcs($ics, $start, $end);
    }

    private function getException()
    {
        /*
        $exception = '';
        if (is_array($plan->getPlanExceptions())) {
            foreach ($plan->getPlanExceptions() as $e) {
                $exception .= 'EXDATE:'.$e->getExcludeDate()->format('Ymd\THis').PHP_EOL;
            }
        }
         */
    }

    private function addEvent(Plan $plan, $vcalendar)
    {
        if ($plan->getRepeating()) {
            foreach ($plan->getPlanRepeats() as $repeat) {
                $vcalendar->add('VEVENT', array(
                    'UID' => $repeat->getIcsUid(),
                    'DTSTAMP' => $plan->getCreatedAt(),
                    'DTSTART' => $plan->getStart(),
                    'DTEND' => $plan->getEnd(),
                    'SUMMARY' => $plan->getName(),
                    'RRULE' => $repeat->getIcsFreq(),
                    'EXPDATE' => '123',
                    'EXPDATE' => '123',
                ));
            }

        } else {

            $vcalendar->add('VEVENT', array(
                'UID' => $plan->getIcsUid(),
                'SUMMARY' => $plan->getName(),
                'DTSTAMP' => $plan->getCreatedAt(),
                'DTSTART' => $plan->getStart(),
                'DTEND' => $plan->getEnd(),
                'EXPDATE' => '123',
                'EXPDATE' => '123',
            ));
        }
    }
}
