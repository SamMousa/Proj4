<?php


namespace Academe\Proj\Traits;

use Academe\Proj\Contracts\Ellipsoid;

/**
 * Trait DeriveGeodeticTrait
 * Used by classes that have X, Y and Z and want to expose Lat, Lon and H.
 * @package Academe\Proj\Traits
 */
trait DeriveGeodeticTrait
{
    /**
     * @var float[] The derived values.
     */
    private $_geodeticTrait = [];

    /**
     * @return Ellipsoid
     */
    abstract public function getEllipsoid();

    abstract public function getX();

    abstract public function getY();

    abstract public function getZ();

    private function calculateGeodetic()
    {
        $x = $this->getX();
        $y = $this->getY();
        $z = $this->getZ();

        // Local definitions and variables
        // end-criterium of loop, accuracy of sin(Latitude)

        $genau = 1.0E-12;
        $genau2 = $genau * $genau;
        $maxiter = 30;

        /*
        $P;        // distance between semi-minor axis and location
        $RR;       // distance between center and location
        $CT;       // sin of geocentric latitude
        $ST;       // cos of geocentric latitude
        $RX;
        $RK;
        $RN;       // Earth radius at location
        $CPHI0;    // cos of start or old geodetic latitude in iterations
        $SPHI0;    // sin of start or old geodetic latitude in iterations
        $CPHI;     // cos of searched geodetic latitude
        $SPHI;     // sin of searched geodetic latitude
        $SDPHI;    // end-criterium: addition-theorem of sin(Latitude(iter)-Latitude(iter-1))
        $at_pole;     // indicates location is in polar region
        $iter;        // of continous iteration, max. 30 is always enough (s.a.)
        $lon;
        $lat;
        $height;
        */

        $a = $this->getEllipsoid()->getA();
        $b = $this->getEllipsoid()->getB();
        // The eccentricity squared.
        $es = $this->getEllipsoid()->getEs();


        $at_pole = false;

        // The distance from the axis passing through the poles.
        $P = sqrt($x * $x + $y * $y);
        $RR = sqrt($x * $x + $y * $y + $z * $z);

        // Special cases for latitude and longitude.
        if ($P / $a < $genau) {
            // Special case: at the poles if P=0. (X=0, Y=0)
            $at_pole = true;
            $lon = 0.0;

            // If (X,Y,Z)=(0,0,0) then Height becomes semi-minor axis
            // of ellipsoid (=center of mass) and Latitude becomes PI/2

            if ($RR / $a < $genau) {
                $lat = M_PI_2;
                $height = -$b;
                return;
            }
        } else {
            // Ellipsoidal (geodetic) longitude interval:
            // -PI < Longitude <= +PI
            $lon = atan2($y, $x);
        }


        /* --------------------------------------------------------------
         * Following iterative algorithm was developped by
         * "Institut für Erdmessung", University of Hannover, July 1988.
         * Internet: www.ife.uni-hannover.de
         * Iterative computation of CPHI,SPHI and Height.
         * Iteration of CPHI and SPHI to 10**-12 radian res$p->
         * 2*10**-7 arcsec.
         * --------------------------------------------------------------
         */

        $CT = $z / $RR;
        $ST = $P / $RR;
        $RX = 1.0 / sqrt(1.0 - $es * (2.0 - $es) * $ST * $ST);
        $CPHI0 = $ST * (1.0 - $es) * $RX;
        $SPHI0 = $CT * $RX;
        $iter = 0;

        // Loop to find sin(Latitude) res $p-> Latitude
        // until |sin(Latitude(iter)-Latitude(iter-1))| < genau

        do {
            ++$iter;

            $RN = $a / sqrt(1.0 - $es * $SPHI0 * $SPHI0);

            // Ellipsoidal (geodetic) height
            $height = $P * $CPHI0 + $z * $SPHI0 - $RN * (1.0 - $es * $SPHI0 * $SPHI0);

            $RK = $es * $RN / ($RN + $height);
            $RX = 1.0 / sqrt(1.0 - $RK * (2.0 - $RK) * $ST * $ST);
            $CPHI = $ST * (1.0 - $RK) * $RX;
            $SPHI = $CT * $RX;
            $SDPHI = $SPHI * $CPHI0 - $CPHI * $SPHI0;
            $CPHI0 = $CPHI;
            $SPHI0 = $SPHI;
        } while ($SDPHI * $SDPHI > $genau2 && $iter < $maxiter);

        // Ellipsoidal (geodetic) latitude
        $lat = atan($SPHI / abs($CPHI));


        $this->_geodeticTrait = [
            'lat' => rad2deg($lat),
            'lon' => rad2deg($lon),
            'h' => $height,
        ];

    }

    /**
     * @return float
     */
    public function getLat()
    {
        if (!isset($this->_geodeticTrait['lat'])) {
            $this->calculateGeodetic();
        }
        return $this->_geodeticTrait['lat'];
    }

    /**
     * @return float
     */
    public function getLon()
    {
        if (!isset($this->_geodeticTrait['lon'])) {
            $this->calculateGeodetic();
        }
        return $this->_geodeticTrait['lon'];
    }

    /**
     * @return float
     */
    public function getH()
    {
        if (!isset($this->_geodeticTrait['h'])) {
            $this->calculateGeodetic();
        }
        return $this->_geodeticTrait['h'];
    }
}