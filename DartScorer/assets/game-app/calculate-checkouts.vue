<template>
  <div>
    <p v-if="calculate_checkouts(score).length > 0" style="margin: 0px;">
      {{
        stringify_checkout(calculate_checkouts(score)[0][0], calculate_checkouts(score)[0][1], calculate_checkouts(score)[0][2])
      }}
    </p>

    <p v-if="calculate_checkouts(score).length > 1" style="margin: 0px;">
      {{
        stringify_checkout(calculate_checkouts(score)[1][0], calculate_checkouts(score)[1][1], calculate_checkouts(score)[1][2])
      }}
    </p>

    <p v-if="calculate_checkouts(score).length > 2" style="margin: 0px;">
      {{
        stringify_checkout(calculate_checkouts(score)[2][0], calculate_checkouts(score)[2][1], calculate_checkouts(score)[2][2])
      }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'CalculateCheckouts',
  props: {
    score: null,
  },
  methods: {
    /*
    # Focus on as few darts as possible to check out
    # Focus on highest checkout doubles 40, 38, 36
    # Show fastest Bull-Checkout, show only one Bull-Checkout
    */
    calculate_checkouts(score) {
      const max_scores_per_first_arrow = [60, 57, 54, 51, 50, 48, 45, 42, 40, 38, 36, 34, 33, 32, 30, 28, 26, 25, 24, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
      const max_scores_per_second_arrow = [60, 57, 54, 51, 50, 48, 45, 42, 40, 38, 36, 34, 33, 32, 30, 28, 26, 25, 24, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
      const max_checkouts_per_arrow = [50, 40, 38, 36, 34, 32, 30, 28, 26, 24, 22, 20, 18, 16, 14, 12, 10, 8, 6, 4, 2];
      const checkouts_preferred = [];
      const checkouts_one_dart = [];
      const checkouts_two_dart = [];
      const checkouts_three_dart = [];
      let bull_used = false;

      for (let f = 0; f < max_scores_per_first_arrow.length; f++) {
        for (let s = 0; s < max_scores_per_second_arrow.length; s++) {
          const score_remaining = score - max_scores_per_first_arrow[f] - max_scores_per_second_arrow[s];

          if (score_remaining >= 2) {
            for (const c of max_checkouts_per_arrow) {

              if (score_remaining - c === 0) {

                if (max_scores_per_first_arrow[f] <= max_scores_per_second_arrow[s]) {

                  if (max_scores_per_first_arrow[f] === 0 && max_scores_per_second_arrow[s] === 0) {
                    const values = [max_scores_per_first_arrow[f], max_scores_per_second_arrow[s], c];
                    if (c === 50 && !bull_used) {
                      bull_used = true;
                      checkouts_one_dart.push(values);
                    } else {
                      checkouts_one_dart.push(values);
                    }
                  }

                  if (max_scores_per_first_arrow[f] === 0 && max_scores_per_second_arrow[s] !== 0) {
                    const values = [max_scores_per_first_arrow[f], max_scores_per_second_arrow[s], c];
                    if (c === 50 && !bull_used) {
                      bull_used = true;
                      checkouts_two_dart.push(values);
                    } else {
                      checkouts_two_dart.push(values);
                    }
                  }
                }

                if (max_scores_per_first_arrow[f] >= max_scores_per_second_arrow[s]) {

                  if (max_scores_per_first_arrow[f] !== 0 && max_scores_per_second_arrow[s] !== 0) {
                    const values = [max_scores_per_first_arrow[f], max_scores_per_second_arrow[s], c];
                    if (!bull_used) {
                      bull_used = true;
                      checkouts_three_dart.push(values);
                    } else if (c !== 50) {
                      checkouts_three_dart.push(values);
                    }
                  }
                }
              }
            }
          }
        }
      }

      checkouts_preferred.push(...checkouts_one_dart.sort((a, b) => b[0] - a[0]));

      if (checkouts_preferred.length === 0) {
        checkouts_preferred.push(...checkouts_two_dart.sort((a, b) => b[2] - a[2]).slice(0, 3));

        if (checkouts_preferred.length === 0) {
          checkouts_preferred.push(...checkouts_three_dart.sort((a, b) => b[0] - a[0] || b[2] - a[2]).slice(0, 3));
        } else if (checkouts_preferred.length === 1) {
          checkouts_preferred.push(...checkouts_three_dart.sort((a, b) => b[0] - a[0] || b[2] - a[2]).slice(0, 2));
        } else if (checkouts_preferred.length === 2) {
          checkouts_preferred.push(...checkouts_three_dart.sort((a, b) => b[0] - a[0] || b[2] - a[2]).slice(0, 1));
        }
      } else if (checkouts_preferred.length === 1) {
        checkouts_preferred.push(...checkouts_two_dart.sort((a, b) => b[2] - a[2]).slice(0, 2));
        if (checkouts_preferred.length === 1) {
          checkouts_preferred.push(...checkouts_three_dart.sort((a, b) => b[0] - a[0] || b[2] - a[2]).slice(0, 2));
        } else if (checkouts_preferred.length === 2) {
          checkouts_preferred.push(...checkouts_three_dart.sort((a, b) => b[0] - a[0] || b[2] - a[2]).slice(0, 1));
        }
      } else if (checkouts_preferred.length === 2) {
        checkouts_preferred.push(...checkouts_two_dart.sort((a, b) => b[2] - a[2]).slice(0, 1));
        if (checkouts_preferred.length === 2) {
          checkouts_preferred.push(...checkouts_three_dart.sort((a, b) => b[0] - a[0] || b[2] - a[2]).slice(0, 1));
        }
      }

      return checkouts_preferred;
    },

    // Input: 0, 50, 40
    // Output: 0, BULL, D20
    stringify_checkout(f, s, c) {
      let f_s, s_s, c_s;

      if (f <= 20) {
        f_s = String(f);
      } else if (f === 25) {
        f_s = "25";
      } else if (f === 50) {
        f_s = "BULL";
      } else if (f >= 21 && f <= 40) {
        if (f % 2 === 0) {
          const f_n = f / 2;
          f_s = String(Math.floor(f_n));
          f_s = "D" + f_s;
        } else if (f % 3 === 0) {
          const f_n = f / 3;
          f_s = String(Math.floor(f_n));
          f_s = "T" + f_s;
        }
      } else if (f >= 41 && s <= 60) {
        if (f % 3 === 0) {
          const f_n = f / 3;
          f_s = String(Math.floor(f_n));
          f_s = "T" + f_s;
        }
      }

      if (s <= 20) {
        s_s = String(s);
      } else if (s === 25) {
        s_s = "25";
      } else if (s === 50) {
        s_s = "BULL";
      } else if (s >= 21 && s <= 40) {
        if (s % 2 === 0) {
          const s_n = s / 2;
          s_s = String(Math.floor(s_n));
          s_s = "D" + s_s;
        } else if (s % 3 === 0) {
          const s_n = s / 3;
          s_s = String(Math.floor(s_n));
          s_s = "T" + s_s;
        }
      } else if (s >= 41 && s <= 60) {
        if (s % 3 === 0) {
          const s_n = s / 3;
          s_s = String(Math.floor(s_n));
          s_s = "T" + s_s;
        }
      }

      if (c === 50) {
        c_s = "BULL";
      } else if (c % 2 === 0) {
        const c_n = c / 2;
        c_s = String(Math.floor(c_n));
        c_s = "D" + c_s;
      }

      return f_s + ", " + s_s + ", " + c_s;
    },
  }
};
</script>