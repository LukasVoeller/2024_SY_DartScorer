# Focus on as few darts as possible to checkout
# Focus on highest checkout doubles 40, 38, 36
# Show fastest Bull-Checkout, show only one Bull-Checkout

score = int(input("Enter a value: "))

def calculate_checkouts(score):
    max_scores_per_first_arrow = [60, 57, 54, 51, 50, 48, 45, 42, 40, 38, 36, 34, 33, 32, 30, 28, 26, 25, 24, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
    max_scores_per_second_arrow = [60, 57, 54, 51, 50, 48, 45, 42, 40, 38, 36, 34, 33, 32, 30, 28, 26, 25, 24, 22, 21, 20, 19, 18, 17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
    max_checkouts_per_arrow = [50, 40, 38, 36, 34, 32, 30, 28, 26, 24, 22, 20, 18, 16, 14, 12, 10, 8, 6, 4, 2]

    checkouts_preferred  = []

    checkouts_one_dart = []
    checkouts_two_dart = []
    checkouts_three_dart = []

    # Flag to track if BULL-Checkout has been used
    # Used to display a BULL-Checkout only once
    bull_used = False

    for f in range(len(max_scores_per_first_arrow)):
        for s in range(len(max_scores_per_second_arrow)):
            score_remaining = score - max_scores_per_first_arrow[f] - max_scores_per_second_arrow[s]
            # No checkout possible, skip
            if score_remaining >= 2:
                for c in max_checkouts_per_arrow:
                    # CHECKOUT!
                    if score_remaining - c == 0:
                        # One- and Two-Darter, first and second arrows have to be <= 0
                        if max_scores_per_first_arrow[f] <= max_scores_per_second_arrow[s]:
                            # One-Darter
                            if max_scores_per_first_arrow[f] == 0 and max_scores_per_second_arrow[s] == 0:
                                values = [max_scores_per_first_arrow[f], max_scores_per_second_arrow[s], c]
                                if c == 50 and not bull_used:
                                    bull_used = True
                                    checkouts_one_dart.append(values)
                                else:
                                    checkouts_one_dart.append(values)
                            # Two-Darter
                            if max_scores_per_first_arrow[f] == 0 and max_scores_per_second_arrow[s] != 0:
                                values = [max_scores_per_first_arrow[f], max_scores_per_second_arrow[s], c]
                                if c == 50 and not bull_used:
                                    bull_used = True
                                    checkouts_two_dart.append(values)
                                else:
                                    checkouts_two_dart.append(values)
                        # Three-Darter, first a arrow has to be larger than the second
                        if max_scores_per_first_arrow[f] >= max_scores_per_second_arrow[s]:
                            # Three-Darter
                            if max_scores_per_first_arrow[f] != 0 and max_scores_per_second_arrow[s] != 0:
                                values = [max_scores_per_first_arrow[f], max_scores_per_second_arrow[s], c]
                                if not bull_used:
                                    bull_used = True
                                    checkouts_three_dart.append(values)
                                elif c != 50:
                                    checkouts_three_dart.append(values)

    #print(score , "\tleft\tOne Dart COs:", len(checkouts_one_dart), "\tTwo Dart COs:", len(checkouts_two_dart), "\tThree Dart COs:", len(checkouts_three_dart))
    #print(checkouts_three_dart)

    # Put together the Checkout selection, first use One-Darters, then Two- and then Three-Darters
    # The maxium of checkout possibilities shown is three
    # Add One-Dart Checkouts (there are only 21, see max_checkouts_per_arrow)
    # key=lambda x: x[0] -> Sort by first arrow
    # key=lambda x: x[1] -> Sort by second arrow
    # key=lambda x: x[2] -> Sort by checkout arrow
    checkouts_preferred.extend(sorted(checkouts_one_dart, key=lambda x: x[0], reverse=True))

    # No One-Dart Checkouts, Add Two-Dart Checkouts
    if len(checkouts_preferred) == 0:
        checkouts_preferred.extend(sorted(checkouts_two_dart, key=lambda x: x[2], reverse=True)[:3])

        # No Two-Dart Checkouts, Add Three-Dart Checkouts
        if len(checkouts_preferred) == 0:
            checkouts_preferred.extend(sorted(checkouts_three_dart, key=lambda x: (x[0] , x[2]), reverse=True)[:3])
        elif len(checkouts_preferred) == 1:
            checkouts_preferred.extend(sorted(checkouts_three_dart, key=lambda x: (x[0] , x[2]), reverse=True)[:2])
        elif len(checkouts_preferred) == 2:
            checkouts_preferred.extend(sorted(checkouts_three_dart, key=lambda x: (x[0] , x[2]), reverse=True)[:1])
    elif len(checkouts_preferred) == 1:
        checkouts_preferred.extend(sorted(checkouts_two_dart, key=lambda x: x[2], reverse=True)[:2])

        if len(checkouts_preferred) == 1:
            checkouts_preferred.extend(sorted(checkouts_three_dart, key=lambda x: (x[0] , x[2]), reverse=True)[:2])
        elif len(checkouts_preferred) == 2:
            checkouts_preferred.extend(sorted(checkouts_three_dart, key=lambda x: (x[0] , x[2]), reverse=True)[:1])
    elif len(checkouts_preferred) == 2:
        checkouts_preferred.extend(sorted(checkouts_two_dart, key=lambda x: x[2], reverse=True)[:1])

        if len(checkouts_preferred) == 2:
            checkouts_preferred.extend(sorted(checkouts_three_dart, key=lambda x: (x[0] , x[2]), reverse=True)[:1])

    return checkouts_preferred


def stringify_checkout(f, s, c):

    if f <= 20:
        f_s = str(f)
    elif f == 25:
        f_s = "25"
    elif f == 50:
        f_s = "BULL"
    elif f >= 21 and f <= 40:
        if f % 2 == 0:
            f_n = f/2
            f_s = str(int(f_n))
            f_s = "D" + f_s
        elif f % 3 == 0:
            f_n = f/3
            f_s = str(int(f_n))
            f_s = "T" + f_s
    elif f >= 41 and s <= 60:
        if f % 3 == 0:
            f_n = f/3
            f_s = str(int(f_n))
            f_s = "T" + f_s

    if s <= 20:
        s_s = str(s)
    elif s == 25:
        s_s = "25"
    elif s == 50:
        s_s = "BULL"
    elif s >= 21 and s <= 40:
        if s % 2 == 0:
            s_n = s/2
            s_s = str(int(s_n))
            s_s = "D" + s_s
        elif s % 3 == 0:
            s_n = s/3
            s_s = str(int(s_n))
            s_s = "T" + s_s
    elif s >= 41 and s <= 60:
        if s % 3 == 0:
            s_n = s/3
            s_s = str(int(s_n))
            s_s = "T" + s_s

    if c == 50:
        c_s = "BULL"
    elif c % 2 == 0:
        c_n = c/2
        c_s = str(int(c_n))
        c_s = "D" + c_s

    return f_s + ", " + s_s + ", " + c_s

#for no in range(171):
    #checkouts = calculate_checkouts(no)

checkouts = calculate_checkouts(score)
#print(stringify_checkout(0,1,4))

print("Possible checkouts:")
for values in checkouts:
    #print("Checkout:", values)
    #print("Sort by: ", values[1])
    print("Checkout:", stringify_checkout(values[0], values[1], values[2]))