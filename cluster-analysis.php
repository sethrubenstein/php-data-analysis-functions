<?php
namespace sethrubenstein;

class clusterAnalysis {

    /**
     * @param array $clusters
     * @param array $answers
     * @param string $operation
     * @param null $raw
     * @return mixed|null
     */
    public function euclidean_distance($clusters = array(), $answers = array(), $operation = 'winner', $raw = null ) {
		$final_calculations = array(); // Here is where our final euclidean distances for all the columns will reside.
		$squared_distances = array();

		foreach ($clusters as $key => $value) {
			$squared_distances[$key] = array();
			$i = 0;
			foreach ($answers as $answer) {
				$x = pow( ($clusters[$key][$i] - $answers[$i]), 2);
				$squared_distances[$key][$i] = $x;
				$i++;
			}
			
			$euclidean_distance = sqrt( array_sum($squared_distances[$key]) );
      
			if ( 'groups' == $operation ) {
				$final_calculations[$key] = $euclidean_distance;
			} else {
				$final_calculations[] = $euclidean_distance;
			}
		}

		if ( 'winner' == $operation ) {
			$winner = min($final_calculations);
			return $winner;
		} elseif ( 'groups' == $operation ) {
			foreach ($final_calculations as $key => $value) {
				$raw['groups'][$key]['score'] = $value;
			}
			return $raw;
		}
	}

    /**
     * @param array $clusters
     * @param array $probabilities
     * @param array $answers
     * @param string $operation
     * @param null $raw
     * @return mixed|null
     */
    public function logarithmic_probability($clusters = array(), $probabilities = array(), $answers = array(), $operation = 'winner', $raw = null ) {
		$final_calculations = array();
		$logged_probabilities = array();

		foreach ($clusters as $key => $value) {
			$logged_probabilities[$key] = array();
			$i = 0;
			foreach ($answers as $answer) {
				$x = $clusters[$key][$i]*($answers[$i]+0.001)+(1-$clusters[$key][$i])*(1-$answers[$i]+0.001);
				$logged_probabilities[$key][$i] = $x;
				$i++;
			}
			// error_log('What do we have thus far'.print_r($logged_probabilities, true));
			$log_probability = ( array_sum($logged_probabilities[$key]) + $probabilities[$key] );
			// error_log('The logarithmic probability of '. $key .' is '.$log_probability);
			if ( 'groups' == $operation ) {
				$final_calculations[$key] = $log_probability;
			} else {
				$final_calculations[] = $log_probability;
			}
		}

		if ( 'winner' == $operation ) {
			$winner = max($final_calculations);
			return $winner;
		} elseif ( 'groups' == $operation ) {
			foreach ($final_calculations as $key => $value) {
				$raw['groups'][$key]['score'] = $value;
			}
			return $raw;
		}
	}

	public function identify_group( $groups, $winner ) {
	    foreach ($groups as $group => $value) {
	        if ( $winner == $value['score'] ) {
	            return $value['name'];
	            break;
            }
        }
		while (list(, $value) = each($groups)) {
		    if ($winner == $value['score']) {
				return $value['name'];
		        break;
		    }
		}
	}
}
